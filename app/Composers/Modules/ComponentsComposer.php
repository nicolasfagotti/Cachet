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

use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\ComponentGroup;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

/**
 * This is the components composer.
 *
 * @author James Brooks <james@alt-three.com>
 * @author Connor S. Parks <connor@connorvg.tv>
 */
class ComponentsComposer
{
    /**
     * The user session object.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $guard;

    /**
     * Creates a new components composer instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $guard
     *
     * @return void
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
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
        // Get the component group if it's defined.
        $viewdata = $view->getData();
        $componentGroup = $viewdata['componentGroup'];

        // Component & Component Group lists.
        $allComponentGroups = $this->getVisibleGroupedComponents();
        if ($componentGroup->exists) {
            $componentGroups = $this->getVisibleGroupedComponents($componentGroup->id);

            $view->withAllComponentGroups($allComponentGroups)
                 ->withComponentGroups($componentGroups)
                 ->withUngroupedComponents(new Collection())
                 ->withComponentGroupSelected($componentGroup);
        } else {
            $ungroupedComponents = Component::ungrouped()->get();

            $view->withAllComponentGroups($allComponentGroups)
                 ->withComponentGroups($allComponentGroups)
                 ->withUngroupedComponents($ungroupedComponents)
                 ->withComponentGroupSelected(null);
        }
    }

    /**
     * Get visible grouped components.
     *
     * @param int|null $componentGroupId
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getVisibleGroupedComponents($componentGroupId = null)
    {
        if ($componentGroupId) {
            $componentGroupsBuilder = ComponentGroup::where('id', $componentGroupId);
        } else {
            $componentGroupsBuilder = ComponentGroup::query();
        }

        if (!$this->guard->check()) {
            $componentGroupsBuilder->visible();
        }

        $usedComponentGroups = Component::grouped()->pluck('group_id');

        return $componentGroupsBuilder->used($usedComponentGroups)
            ->get();
    }
}
