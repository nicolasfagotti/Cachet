<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Handlers\Commands\Component;

use CachetHQ\Cachet\Bus\Commands\Component\AddComponentCommand;
use CachetHQ\Cachet\Bus\Events\Component\ComponentWasAddedEvent;
use CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupStatusWasUpdatedEvent;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\ComponentGroup;

class AddComponentCommandHandler
{
    /**
     * Handle the add component command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Component\AddComponentCommand $command
     *
     * @return \CachetHQ\Cachet\Models\Component
     */
    public function handle(AddComponentCommand $command)
    {
        $componentGroup = new ComponentGroup();
        $originalCGroup = null;

        // If the component will belong to a component group, get the previous component group status.
        if ($command->group_id) {
            $originalCGroup = $componentGroup->find($command->group_id);
            $originalCGroupStatus = $originalCGroup->enabled_components_lowest()->first() ? $originalCGroup->enabled_components_lowest()->first()->status : 0;
        }

        $component = Component::create($this->filter($command));

        event(new ComponentWasAddedEvent($component));

        // Trigger the event for when the component group status is updated.
        if ($command->group_id && $originalCGroup) {
            $newCGroupStatus = $originalCGroup->enabled_components_lowest()->first() ? $originalCGroup->enabled_components_lowest()->first()->status : 0;
            event(new ComponentGroupStatusWasUpdatedEvent($originalCGroup, $originalCGroupStatus, $newCGroupStatus));
        }

        return $component;
    }

    /**
     * Filter the command data.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Incident\AddComponentCommand $command
     *
     * @return array
     */
    protected function filter(AddComponentCommand $command)
    {
        $params = [
            'name'        => $command->name,
            'description' => $command->description,
            'link'        => $command->link,
            'status'      => $command->status,
            'enabled'     => $command->enabled,
            'order'       => $command->order,
            'group_id'    => $command->group_id,
        ];

        return array_filter($params, function ($val) {
            return $val !== null;
        });
    }
}
