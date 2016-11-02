<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Composers\Modules;

use CachetHQ\Cachet\Models\Metric;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\View;

/**
 * This is the metrics composer.
 *
 * @author James Brooks <james@alt-three.com>
 * @author Connor S. Parks <connor@connorvg.tv>
 */
class MetricsComposer
{
    /**
     * The illuminate config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new metrics composer instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Bind data to the view.
     *
     * @param \Illuminate\Contracts\View\View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        // Get the component/group if it's defined.
        $viewdata = $view->getData();
        $component = $viewdata['component'];
        $componentGroup = $viewdata['componentGroup'];

        $metrics = null;
        if ($displayMetrics = $this->config->get('setting.display_graphs')) {
            if ($component->exists) {
                $metrics = Metric::displayable()->where('component_id', '=', $component->id)->orderBy('order')->orderBy('id')->get();
            } elseif ($componentGroup->exists) {
                $metrics = Metric::displayable()->whereIn('component_id', $componentGroup->components()->pluck('id'))->orderBy('order')->orderBy('id')->get();
            } else {
                $metrics = Metric::displayable()->orderBy('order')->orderBy('id')->get();
            }
        }

        $view->withDisplayMetrics($displayMetrics)
            ->withMetrics($metrics);
    }
}
