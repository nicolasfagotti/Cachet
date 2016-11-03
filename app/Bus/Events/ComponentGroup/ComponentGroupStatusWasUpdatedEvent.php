<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Events\ComponentGroup;

use CachetHQ\Cachet\Models\ComponentGroup;

/**
 * This is the component status was updated event.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
final class ComponentGroupStatusWasUpdatedEvent implements ComponentGroupEventInterface
{
    /**
     * The component group that was updated.
     *
     * @var \CachetHQ\Cachet\Models\ComponentGroup
     */
    public $group;

    /**
     * The original status of the component group.
     *
     * @var int
     */
    public $original_status;

    /**
     * The new status of the component group.
     *
     * @var int
     */
    public $new_status;

    /**
     * Create a new component group was updated event instance.
     *
     * @param \CachetHQ\Cachet\Models\ComponentGroup $group
     * @param int                                    $original_status
     * @param int                                    $new_status
     *
     * @return void
     */
    public function __construct(ComponentGroup $group, $original_status, $new_status)
    {
        $this->group = $group;
        $this->original_status = $original_status;
        $this->new_status = $new_status;
    }
}
