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

use CachetHQ\Cachet\Presenters\Traits\TimestampsTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Config;
use McCool\LaravelAutoPresenter\BasePresenter;

class MetricPresenter extends BasePresenter implements Arrayable
{
    use TimestampsTrait;

    /**
     * Determines the metric view filter name.
     *
     * @return string
     */
    public function view_name()
    {
        switch ($this->wrappedObject->default_view) {
            case 0: return 'last_hour';
            case 1: return 'today';
            case 2: return 'week';
            case 3: return 'month';
        }
    }

    /**
     * Determines the metric view filter name, used in the API.
     *
     * @return string
     */
    public function default_view_name()
    {
        return trans('cachet.metrics.filter.'.$this->trans_string_name());
    }

    /**
     * Determines the metric translation view filter name.
     *
     * @return string
     */
    public function trans_string_name()
    {
        switch ($this->wrappedObject->default_view) {
            case 0: return 'last_hour';
            case 1: return 'hourly';
            case 2: return 'weekly';
            case 3: return 'monthly';
        }
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
            'created_at'        => $this->created_at(),
            'updated_at'        => $this->updated_at(),
            'default_view_name' => $this->default_view_name(),
        ]);
    }
}
