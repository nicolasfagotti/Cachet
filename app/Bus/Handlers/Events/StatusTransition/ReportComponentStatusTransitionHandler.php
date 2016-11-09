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
 * This is the report component status transition handler.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class ReportComponentStatusTransitionHandler
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
    }
}
