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


/**
 * This is the update status transition command.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
final class UpdateStatusTransitionCommand
{
    /**
     * The component.
     *
     * @var int
     */
    public $component_id;

    /**
     * The previous command status.
     *
     * @var int
     */
    public $previous_status;

    /**
     * The next command status.
     *
     * @var int
     */
    public $next_status;

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'component_id'    => 'required|int',
        'previous_status' => 'required|int|min:0|max:4',
        'next_status'     => 'required|int|min:0|max:4',
    ];

    /**
     * Create a new report status transition command instance.
     *
     * @param int $component_id
     * @param int $previous_status
     * @param int $next_status
     *
     * @return void
     */
    public function __construct($component_id, $previous_status, $next_status)
    {
        $this->component_id = $component_id;
        $this->previous_status = $previous_status;
        $this->next_status = $next_status;
    }
}
