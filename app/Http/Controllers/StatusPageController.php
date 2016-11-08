<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Controllers;

use AltThree\Badger\Facades\Badger;
use CachetHQ\Cachet\Dates\DateFactory;
use CachetHQ\Cachet\Http\Controllers\Api\AbstractApiController;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\ComponentGroup;
use CachetHQ\Cachet\Models\Incident;
use CachetHQ\Cachet\Models\Metric;
use CachetHQ\Cachet\Models\Schedule;
use CachetHQ\Cachet\Models\StatusTransition;
use CachetHQ\Cachet\Repositories\Metric\MetricRepository;
use Exception;
use GrahamCampbell\Binput\Facades\Binput;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Jenssegers\Date\Date;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

class StatusPageController extends AbstractApiController
{
    /**
     * The metric repository instance.
     *
     * @var \CachetHQ\Cachet\Repositories\Metric\MetricRepository
     */
    protected $metrics;

    /**
     * Construct a new status page controller instance.
     *
     * @param \CachetHQ\Cachet\Repositories\Metric\MetricRepository $metrics
     *
     * @return void
     */
    public function __construct(MetricRepository $metrics)
    {
        $this->metrics = $metrics;
    }

    /**
     * Displays the status page.
     *
     * @param ComponentGroup $componentGroup
     * @param Component      $component
     *
     * @return \Illuminate\View\View
     */
    public function showIndex(ComponentGroup $componentGroup, Component $component)
    {
        $today = Date::now();
        $startDate = Date::now();

        // Check if we have another starting date
        if (Binput::has('start_date')) {
            try {
                // If date provided is valid
                $oldDate = Date::createFromFormat('Y-m-d', Binput::get('start_date'));

                // If trying to get a future date fallback to today
                if ($today->gt($oldDate)) {
                    $startDate = $oldDate;
                }
            } catch (Exception $e) {
                // Fallback to today
            }
        }

        $daysToShow = Config::get('setting.app_incident_days', 0) - 1;
        if ($daysToShow < 0) {
            $daysToShow = 0;
            $incidentDays = [];
        } else {
            $incidentDays = range(0, $daysToShow);
        }

        $incidentVisibility = Auth::check() ? 0 : 1;

        // Find all the visible incidents, taking into account if the component/group is defined and the day limits.
        $allIncidentsQuery = Incident::where('visible', '>=', $incidentVisibility);
        if ($component->exists) {
            $allIncidentsQuery->where('component_id', '=', $component->id);
        } elseif ($componentGroup->exists) {
            $allIncidentsQuery->whereIn('component_id', $componentGroup->components()->pluck('id'));
        }
        $allIncidentsQuery->whereBetween('occurred_at', [
            $startDate->copy()->subDays($daysToShow)->format('Y-m-d').' 00:00:00',
            $startDate->format('Y-m-d').' 23:59:59',
        ])->orderBy('occurred_at', 'desc');
        $allIncidents = $allIncidentsQuery->get()->groupBy(function (Incident $incident) {
            return app(DateFactory::class)->make($incident->occurred_at)->toDateString();
        });

        // Add in days that have no incidents
        if (Config::get('setting.only_disrupted_days') === false) {
            foreach ($incidentDays as $i) {
                $date = app(DateFactory::class)->make($startDate)->subDays($i);

                if (!isset($allIncidents[$date->toDateString()])) {
                    $allIncidents[$date->toDateString()] = [];
                }
            }
        }

        // Sort the array so it takes into account the added days
        $allIncidents = $allIncidents->sortBy(function ($value, $key) {
            return strtotime($key);
        }, SORT_REGULAR, true)->all();

        return View::make('index')
            ->with('component', $component)
            ->with('componentGroup', $componentGroup)
            ->withDaysToShow($daysToShow)
            ->withAllIncidents($allIncidents)
            ->withCanPageForward((bool) $today->gt($startDate))
            ->withCanPageBackward(Incident::where('occurred_at', '<', $startDate->format('Y-m-d'))->count() > 0)
            ->withPreviousDate($startDate->copy()->subDays($daysToShow)->toDateString())
            ->withNextDate($startDate->copy()->addDays($daysToShow)->toDateString());
    }

    /**
     * Shows an incident in more detail.
     *
     * @param \CachetHQ\Cachet\Models\Incident $incident
     *
     * @return \Illuminate\View\View
     */
    public function showIncident(Incident $incident)
    {
        return View::make('single-incident')->withIncident($incident);
    }

    /**
     * Show a single schedule.
     *
     * @param \CachetHQ\Cachet\Models\Schedule $schedule
     *
     * @return \Illuminate\View\View
     */
    public function showSchedule(Schedule $schedule)
    {
        return View::make('single-schedule')->withSchedule($schedule);
    }

    /**
     * Returns metrics in a readily formatted way.
     *
     * @param \CachetHQ\Cachet\Models\Metric $metric
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics(Metric $metric)
    {
        $metricData = [];
        $type = Binput::get('filter', 'last_hour');

        switch ($type) {
            case 'last_hour':
                $metricData = $this->metrics->listPointsLastHour($metric);
                break;
            case 'today':
                $metricData = $this->metrics->listPointsToday($metric);
                break;
            case 'week':
                $metricData = $this->metrics->listPointsForWeek($metric);
                break;
            case 'month':
                $metricData = $this->metrics->listPointsForMonth($metric);
                break;
        }

        return $this->item([
            'metric' => $metric->toArray(),
            'items'  => $metricData,
        ]);
    }

    /**
     * Generates a Shield (badge) for the component.
     *
     * @param \CachetHQ\Cachet\Models\Component $component
     *
     * @return \Illuminate\Http\Response
     */
    public function showComponentBadge(Component $component)
    {
        $component = AutoPresenter::decorate($component);
        $color = null;

        switch ($component->status_color) {
            case 'reds':
                $color = Config::get('setting.style_reds', '#ff6f6f');
                break;
            case 'blues':
                $color = Config::get('setting.style_blues', '#3498db');
                break;
            case 'greens':
                $color = Config::get('setting.style_greens', '#7ED321');
                break;
            case 'yellows':
                $color = Config::get('setting.style_yellows', '#F7CA18');
                break;
        }

        $badge = Badger::generate(
            $component->name,
            $component->human_status,
            substr($color, 1),
            Binput::get('style', 'flat-square')
        );

        return Response::make($badge, 200, ['Content-Type' => 'image/svg+xml']);
    }

    /**
     * Return all the status transitions between two dates for the component.
     *
     * @param \CachetHQ\Cachet\Models\Component $component
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentStatusTransitions(Component $component)
    {
        $fromDate = date('Y-m-d H:i:s', strtotime(Binput::get('from')));
        $toDate = date('Y-m-d H:i:s', strtotime(Binput::get('to')));

        // Get the status transition between the dates defined.
        $statusTransitions = StatusTransition::where('component_id', '=', $component->id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the previous status transition to the dates queried.
        $previousStatusTransitions = StatusTransition::where('component_id', '=', $component->id)
            ->where('created_at', '<', $fromDate)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return $this->item([
            'transitions'         => $statusTransitions,
            'previous_transition' => $previousStatusTransitions,
        ]);
    }

    /**
     * Return all the status transitions between two dates for the component group.
     *
     * @param \CachetHQ\Cachet\Models\ComponentGroup $componentGroup
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentGroupStatusTransitions(ComponentGroup $componentGroup)
    {
        $fromDate = date('Y-m-d H:i:s', strtotime(Binput::get('from')));
        $toDate = date('Y-m-d H:i:s', strtotime(Binput::get('to')));

        $statusTransitions = StatusTransition::where('component_group_id', '=', $componentGroup->id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the previous status transition to the dates queried.
        $previousStatusTransitions = StatusTransition::where('component_group_id', '=', $componentGroup->id)
            ->where('created_at', '<', $fromDate)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return $this->item([
            'transitions'         => $statusTransitions,
            'previous_transition' => $previousStatusTransitions,
        ]);
    }
}
