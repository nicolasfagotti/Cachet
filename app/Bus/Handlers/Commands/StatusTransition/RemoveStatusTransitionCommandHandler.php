<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Handlers\Commands\StatusTransition;

use CachetHQ\Cachet\Bus\Commands\StatusTransition\RemoveStatusTransitionCommand;

/**
 * This is the remove status transition command handler.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class RemoveStatusTransitionCommandHandler
{
    /**
     * Handle the remove status transition command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\StatusTransition\RemoveStatusTransitionCommand $command
     *
     * @return void
     */
    public function handle(RemoveStatusTransitionCommand $command)
    {
        $statusTransition = $command->statusTransition;

        $statusTransition->delete();
    }
}
