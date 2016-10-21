<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Commands\StatusTransition;

use CachetHQ\Cachet\Models\StatusTransition;

/**
 * This is the remove status transition command.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
final class RemoveStatusTransitionCommand
{
    /**
     * The status transition to remove.
     *
     * @var \CachetHQ\Cachet\Models\StatusTransition
     */
    public $statusTransition;

    /**
     * Create a new remove status transition command instance.
     *
     * @param \CachetHQ\Cachet\Models\StatusTransition $statusTransition
     *
     * @return void
     */
    public function __construct(StatusTransition $statusTransition)
    {
        $this->statusTransition = $statusTransition;
    }
}
