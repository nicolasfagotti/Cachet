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

use CachetHQ\Cachet\Bus\Commands\Component\UpdateComponentCommand;
use CachetHQ\Cachet\Bus\Events\Component\ComponentStatusWasUpdatedEvent;
use CachetHQ\Cachet\Bus\Events\Component\ComponentWasUpdatedEvent;
use CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupStatusWasUpdatedEvent;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\ComponentGroup;

class UpdateComponentCommandHandler
{
    /**
     * Handle the update component command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Component\UpdateComponentCommand $command
     *
     * @return \CachetHQ\Cachet\Models\Component
     */
    public function handle(UpdateComponentCommand $command)
    {
        $component = $command->component;
        $componentGroup = new ComponentGroup();
        $originalStatus = $component->status;

        $fromCGroup = $toCGroup = null;
        $originalFromCGroupStatus = $originalToCGroupStatus = 0;

        // If the component belongs to a component group before or after the update, get the component group statuses.
        if ($component->group_id) {
            $fromCGroup = $componentGroup->find($component->group_id);
            $originalFromCGroupStatus = $fromCGroup->enabled_components_lowest()->first() ? $fromCGroup->enabled_components_lowest()->first()->status : 0;
        }
        if ($command->group_id) {
            $toCGroup = $componentGroup->find($command->group_id);
            $originalToCGroupStatus = $toCGroup->enabled_components_lowest()->first() ? $toCGroup->enabled_components_lowest()->first()->status : 0;
        }

        event(new ComponentStatusWasUpdatedEvent($component, $originalStatus, $command->status));

        $component->update($this->filter($command));

        event(new ComponentWasUpdatedEvent($component));

        // Trigger the event for when the component group status is updated.
        if ($fromCGroup) {
            $newFromCGroupStatus = $fromCGroup->enabled_components_lowest()->first() ? $fromCGroup->enabled_components_lowest()->first()->status : 0;
            event(new ComponentGroupStatusWasUpdatedEvent($fromCGroup, $originalFromCGroupStatus, $newFromCGroupStatus));
        }
        if ($toCGroup && $toCGroup != $fromCGroup) {
            $newToCGroupStatus = $toCGroup->enabled_components_lowest()->first() ? $toCGroup->enabled_components_lowest()->first()->status : 0;
            event(new ComponentGroupStatusWasUpdatedEvent($toCGroup, $originalToCGroupStatus, $newToCGroupStatus));
        }

        return $component;
    }

    /**
     * Filter the command data.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Incident\UpdateComponentCommand $command
     *
     * @return array
     */
    protected function filter(UpdateComponentCommand $command)
    {
        $params = [
            'name'          => $command->name,
            'description'   => $command->description,
            'link'          => $command->link,
            'internal_link' => $command->internal_link,
            'status'        => $command->status,
            'enabled'       => $command->enabled,
            'order'         => $command->order,
            'group_id'      => $command->group_id,
        ];

        return array_filter($params, function ($val) {
            return $val !== null;
        });
    }
}
