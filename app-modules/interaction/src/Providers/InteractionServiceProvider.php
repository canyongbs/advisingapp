<?php

namespace Assist\Interaction\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Interaction\InteractionPlugin;

class InteractionServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new InteractionPlugin()));
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
            module: 'interaction',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'interaction',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'interaction',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'interaction',
            path: 'roles/web'
        );
    }
}
