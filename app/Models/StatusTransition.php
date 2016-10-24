<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Models;

use AltThree\Validator\ValidatingTrait;
use CachetHQ\Cachet\Models\Traits\SortableTrait;
use CachetHQ\Cachet\Presenters\StatusTransitionPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * This is the status transition class.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class StatusTransition extends Model implements HasPresenter
{
    use SortableTrait, ValidatingTrait;

    /**
     * The attributes that should be casted to native types.
     *
     * @var string[]
     */
    protected $casts = [
        'component_id'    => 'int',
        'previous_status' => 'int',
        'next_status'     => 'int',
    ];

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'component_id',
        'previous_status',
        'next_status',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'component_id'    => 'required|int',
        'previous_status' => 'required|int',
        'next_status'     => 'required|int',
    ];

    /**
     * The sortable fields.
     *
     * @var string[]
     */
    protected $sortable = [
        'id',
        'component_id',
        'previous_status',
        'next_status',
    ];

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return StatusTransitionPresenter::class;
    }
}
