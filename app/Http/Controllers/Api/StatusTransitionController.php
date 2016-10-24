<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Controllers\Api;

use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\StatusTransition;
use GrahamCampbell\Binput\Facades\Binput;
use Illuminate\Support\Facades\Request;

/**
 * This is the status transition controller.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class StatusTransitionController extends AbstractApiController
{
    /**
     * Return all the status transitions for the component.
     *
     * @param \CachetHQ\Cachet\Models\Component $component
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusTransitions(Component $component)
    {
        $statusTransitions = StatusTransition::where('component_id', '=', $component->id)->orderBy('created_at', 'desc');

        $statusTransitions = $statusTransitions->paginate(Binput::get('per_page', 20));

        return $this->paginator($statusTransitions, Request::instance());
    }

    /**
     * Return all the status transitions between two dates for the component.
     *
     * @param \CachetHQ\Cachet\Models\Component $component
     * @param string                            $fromDate
     * @param string                            $toDate
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusTransitionsByDate(Component $component, $fromDate, $toDate)
    {
        $fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
        $toDate = date('Y-m-d H:i:s', strtotime($toDate));

        $statusTransitions = StatusTransition::where('component_id', '=', $component->id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc');

        $statusTransitions = $statusTransitions->paginate(Binput::get('per_page', 20));

        return $this->paginator($statusTransitions, Request::instance());
    }
}
