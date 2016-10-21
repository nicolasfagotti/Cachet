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
     * Return all updates on the incident.
     *
     * @param \CachetHQ\Cachet\Models\Component $component
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusTransitions(Component $component)
    {
        $statusTransitions = StatusTransition::where('component_id', '=', $component->id)->orderBy('created_at', 'desc');

        if ($sortBy = Binput::get('sort')) {
            $direction = Binput::has('order') && Binput::get('order') == 'desc';

            $statusTransitions->sort($sortBy, $direction);
        }

        $statusTransitions = $statusTransitions->paginate(Binput::get('per_page', 20));

        return $this->paginator($statusTransitions, Request::instance());
    }
}
