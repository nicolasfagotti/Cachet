<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Handlers\Events\StatusTransition;

use CachetHQ\Cachet\Bus\Commands\StatusTransition\ReportStatusTransitionCommand;
use CachetHQ\Cachet\Bus\Events\Component\ComponentStatusWasUpdatedEvent;

/**
 * This is the report status transition handler.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class ReportStatusTransitionHandler
{
    /**
     * Handle the event.
     *
     * @param \CachetHQ\Cachet\Bus\Events\Component\ComponentStatusWasUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ComponentStatusWasUpdatedEvent $event)
    {
        $component = $event->component;
        $componentGroup = $component->group()->first();

        // Don't create the state transition if the status hasn't changed.
        if ($event->original_status === $event->new_status) {
            return;
        }

        // Create the status transition.
        dispatch(new ReportStatusTransitionCommand(
            $component->id,
            0,
            $event->original_status,
            $event->new_status
        ));

        // Stop if the component is not associated to a group.
        if (empty($componentGroup)) {
            return;
        }

        $originalComponentGroupStatus = $componentGroup->enabled_components_lowest()->first()->status;
        $newComponentGroupStatus = $this->getNewComponentGroupStatus($componentGroup->enabled_components_lowest()->get(), $component->id, $event->new_status);

        // Don't create the state transition if the component group status hasn't changed.
        if ($originalComponentGroupStatus === $newComponentGroupStatus) {
            return;
        }

        // Create the component group status transition.
        dispatch(new ReportStatusTransitionCommand(
            0,
            $componentGroup->id,
            $originalComponentGroupStatus,
            $newComponentGroupStatus
        ));
    }

    /**
     * Calculates the new component group status after the component status has changed.
     *
     * @param array $components
     * @param int   $componentId
     * @param int   $newStatus
     *
     * @return int
     */
    private function getNewComponentGroupStatus($components, $componentId, $newStatus)
    {
        foreach ($components as $component) {
            if ($component->id != $componentId) {
                return ($component->status > $newStatus) ? $component->status : $newStatus;
            }
        }

        return 0;
    }
}
