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

use CachetHQ\Cachet\Bus\Commands\StatusTransition\UpdateStatusTransitionCommand;

/**
 * This is the status transition update command handler.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class UpdateStatusTransitionCommandHandler
{
    /**
     * Handle the update status transition command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\StatusTransition\UpdateStatusTransitionCommand $command
     *
     * @return \CachetHQ\Cachet\Models\StatusTransition
     */
    public function handle(UpdateStatusTransitionCommand $command)
    {
        $command->update->update($this->filter($command));

        return $command->update;
    }

    /**
     * Filter the command data.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\StatusTransition\UpdateStatusTransitionCommand $command
     *
     * @return array
     */
    protected function filter(UpdateStatusTransitionCommand $command)
    {
        $params = [
            'component_id'    => $command->component_id,
            'previous_status' => $command->previous_status,
            'next_status'     => $command->next_status,
        ];

        return array_filter($params, function ($val) {
            return $val !== null;
        });
    }
}
