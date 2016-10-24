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

use CachetHQ\Cachet\Bus\Commands\StatusTransition\ReportStatusTransitionCommand;
use CachetHQ\Cachet\Models\StatusTransition;

/**
 * This is the report status transition command handler.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class ReportStatusTransitionCommandHandler
{
    /**
     * Handle the report status transition command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\StatusTransition\ReportStatusTransitionCommand $command
     *
     * @return \CachetHQ\Cachet\Models\StatusTransition
     */
    public function handle(ReportStatusTransitionCommand $command)
    {
        $data = [
            'component_id'    => $command->component_id,
            'previous_status' => $command->previous_status,
            'next_status'     => $command->next_status,
        ];

        // Create the status transition.
        $statusTransition = StatusTransition::create($data);

        return $statusTransition;
    }
}
