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

class ComponentPresenter extends BasePresenter implements Arrayable
{
    use TimestampsTrait;

    /**
     * Returns the override class name for theming.
     *
     * @return string
     */
    public function status_color()
    {
        switch ($this->wrappedObject->status) {
            case 0:
                return 'greys';
            case 1:
                return 'greens';
            case 2:
                return 'blues';
            case 3:
                return 'yellows';
            case 4:
                return 'reds';
        }
    }

    /**
     * Looks up the human readable version of the status.
     *
     * @return string
     */
    public function human_status()
    {
        return trans('cachet.components.status.'.$this->wrappedObject->status);
    }

    /**
     * Find all tag names for the component names.
     *
     * @return array
     */
    public function tags()
    {
        return $this->wrappedObject->tags->pluck('name', 'slug');
    }

    /**
     * Present formatted date time.
     *
     * @return string
     */
    public function updated_at_formatted()
    {
        return ucfirst(app(DateFactory::class)->make($this->wrappedObject->updated_at)->format(Config::get('setting.incident_date_format', 'l jS F Y H:i:s')));
    }

    /**
     * Present the Atlas Query Editor link based on the internal link field.
     *
     * @return string
     */
    public function atlas_query_editor_link()
    {
        $atlasQueryEditorHost = 'http://atlas-query-editor.' . Config::get('agora.env') . '.agora.odesk.com';

        if (filter_var($this->wrappedObject->internal_link, FILTER_VALIDATE_URL) !== false) {
            $urlParts = parse_url($this->wrappedObject->internal_link);
            $linkQuery = isset($urlParts['query']) ? $urlParts['query'] : '';
            $atlasHostAPI = preg_replace('/\?.*/', '', $this->wrappedObject->internal_link);
            $atlasHostAPI = preg_replace('/\/graph$/', '', $atlasHostAPI);
        } else {
            $linkQuery = '';
            $atlasHostAPI = 'http://atlas.' . Config::get('agora.env') . '.agora.odesk.com:7101/api/v1';
        }

        return $atlasQueryEditorHost . '#?' . $linkQuery . '&host=' . $atlasHostAPI;
    }

    /**
     * Convert the presenter instance to an array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'created_at'  => $this->created_at(),
            'updated_at'  => $this->updated_at(),
            'status_name' => $this->human_status(),
            'tags'        => $this->tags(),
        ]);
    }
}
