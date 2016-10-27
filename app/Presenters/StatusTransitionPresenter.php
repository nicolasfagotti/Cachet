<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Presenters;

use CachetHQ\Cachet\Dates\DateFactory;
use CachetHQ\Cachet\Presenters\Traits\TimestampsTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Config;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * This is the status transition presenter.
 *
 * @author Nicolas Fagotti <nicolasfagotti@gmail.com>
 */
class StatusTransitionPresenter extends BasePresenter implements Arrayable
{
    use TimestampsTrait;

    /**
     * Present formatted date time.
     *
     * @return string
     */
    public function created_at_iso()
    {
        return app(DateFactory::class)->make($this->wrappedObject->created_at)->toISO8601String();
    }

    /**
     * Present formatted date time.
     *
     * @return string
     */
    public function updated_at_iso()
    {
        return app(DateFactory::class)->make($this->wrappedObject->updated_at)->toISO8601String();
    }

    /**
     * Returns a human readable version of the previous status.
     *
     * @return string
     */
    public function human_previous_status()
    {
        return trans('cachet.components.status.'.$this->wrappedObject->previous_status);
    }

    /**
     * Returns a human readable version of the next status.
     *
     * @return string
     */
    public function human_next_status()
    {
        return trans('cachet.components.status.'.$this->wrappedObject->next_status);
    }

    /**
     * Convert the presenter status transition to an array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'human_previous_status' => $this->human_previous_status(),
            'human_next_status'     => $this->human_next_status(),
            'utc_created_at'        => $this->created_at_iso(),
            'utc_updated_at'        => $this->updated_at_iso(),
        ]);
    }
}
