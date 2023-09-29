<?php

namespace Assist\Timeline\Providers;

use Filament\Panel;
use Assist\Timeline\TimelinePlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class TimelineServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new TimelinePlugin()));
    }

    public function boot()
    {
        Relation::morphMap([]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'timeline',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'timeline',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'timeline',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'timeline',
            path: 'roles/web'
        );
    }
}
