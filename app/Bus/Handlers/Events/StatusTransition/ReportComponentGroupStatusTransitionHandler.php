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
use CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupStatusWasUpdatedEvent;

/**
 * This is the report status transition handler for component groups.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class ReportComponentGroupStatusTransitionHandler
{
    /**
     * Handle the event.
     *
     * @param \CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupStatusWasUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ComponentGroupStatusWasUpdatedEvent $event)
    {
        $componentGroup = $event->group;

        // Don't create the state transition if the status hasn't changed.
        if ($event->original_status === $event->new_status) {
            return;
        }

        // Create the component group status transition.
        dispatch(new ReportStatusTransitionCommand(
            0,
            $componentGroup->id,
            $event->original_status,
            $event->new_status
        ));
    }
}
