<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

/**
 * This is the status page routes class.
 *
 * @author James Brooks <james@alt-three.com>
 */
class StatusPageRoutes
{
    /**
     * Define the status page routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group([
            'middleware' => ['web', 'ready', 'localize'],
        ], function (Registrar $router) {
            $router->get('/', [
                'as'   => 'get:status-page',
                'uses' => 'StatusPageController@showIndex',
            ]);

            $router->get('incidents/{incident}', [
                'as'   => 'get:incident',
                'uses' => 'StatusPageController@showIncident',
            ]);

            $router->get('metrics/{metric}', [
                'as'   => 'get:metric',
                'uses' => 'StatusPageController@getMetrics',
            ]);

            $router->get('group/{componentGroup}', [
                'as'   => 'get:component-status-page',
                'uses' => 'StatusPageController@showIndex',
            ]);

            $router->get('component/{component}/shield', [
                'as'   => 'get:component_shield',
                'uses' => 'StatusPageController@showComponentBadge',
            ]);
        });
    }
}
