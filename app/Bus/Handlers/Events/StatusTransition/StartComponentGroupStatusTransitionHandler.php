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
use CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupWasAddedEvent;

/**
 * This is the handler for the first status transition.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class StartComponentGroupStatusTransitionHandler
{
    /**
     * Handle the event.
     *
     * @param \CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupWasAddedEvent $event
     *
     * @return void
     */
    public function handle(ComponentGroupWasAddedEvent $event)
    {
        $componentGroup = $event->group;

        // Create the component group status transition.
        dispatch(new ReportStatusTransitionCommand(
            0,
            $componentGroup->id,
            0,
            0
        ));
    }
}
