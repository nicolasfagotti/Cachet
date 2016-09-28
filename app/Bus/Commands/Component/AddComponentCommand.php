<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Commands\Component;

final class AddComponentCommand
{
    /**
     * The component name.
     *
     * @var string
     */
    public $name;

    /**
     * The component description.
     *
     * @var string
     */
    public $description;

    /**
     * The component status.
     *
     * @var int
     */
    public $status;

    /**
     * The component link.
     *
     * @var string
     */
    public $link;

    /**
     * The component internal link.
     *
     * @var string
     */
    public $internal_link;

    /**
     * The component order.
     *
     * @var int
     */
    public $order;

    /**
     * The component group.
     *
     * @var int
     */
    public $group_id;

    /**
     * Is the component enabled?
     *
     * @var bool
     */
    public $enabled;

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'name'          => 'required|string',
        'description'   => 'string',
        'status'        => 'int|min:1|max:4',
        'link'          => 'url',
        'internal_link' => 'url',
        'order'         => 'int',
        'group_id'      => 'int',
        'enabled'       => 'bool',
    ];

    /**
     * Create a new add component command instance.
     *
     * @param string $name
     * @param string $description
     * @param int    $status
     * @param string $link
     * @param string $internal_link
     * @param int    $order
     * @param int    $group_id
     * @param bool   $enabled
     *
     * @return void
     */
    public function __construct($name, $description, $status, $link, $internal_link, $order, $group_id, $enabled)
    {
        $this->name = $name;
        $this->description = $description;
        $this->status = (int) $status;
        $this->link = $link;
        $this->internal_link = $internal_link;
        $this->order = $order;
        $this->group_id = $group_id;
        $this->enabled = $enabled;
    }
}
