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

use CachetHQ\Cachet\Bus\Commands\Component\RemoveComponentCommand;
use CachetHQ\Cachet\Bus\Events\Component\ComponentWasRemovedEvent;
use CachetHQ\Cachet\Bus\Events\ComponentGroup\ComponentGroupStatusWasUpdatedEvent;
use CachetHQ\Cachet\Models\ComponentGroup;

class RemoveComponentCommandHandler
{
    /**
     * Handle the remove component command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Component\RemoveComponentCommand $command
     *
     * @return void
     */
    public function handle(RemoveComponentCommand $command)
    {
        $component = $command->component;
        $componentGroup = new ComponentGroup();
        $originalCGroup = null;

        // If the component belonged to a component group, get the previous component group status.
        if ($component->group_id) {
            $originalCGroup = $componentGroup->find($component->group_id);
            $originalCGroupStatus = $originalCGroup->enabled_components_lowest()->first() ? $originalCGroup->enabled_components_lowest()->first()->status : 0;
        }

        event(new ComponentWasRemovedEvent($component));

        $component->delete();

        // Trigger the event for when the component is deleted.
        if ($component->group_id && $originalCGroup) {
            $newCGroupStatus = $originalCGroup->enabled_components_lowest()->first() ? $originalCGroup->enabled_components_lowest()->first()->status : 0;
            event(new ComponentGroupStatusWasUpdatedEvent($originalCGroup, $originalCGroupStatus, $newCGroupStatus));
        }
    }
}
