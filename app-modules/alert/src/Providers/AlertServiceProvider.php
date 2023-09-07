<?php

namespace Assist\Alert\Providers;

use Filament\Panel;
use Assist\Alert\AlertPlugin;
use Assist\Alert\Models\Alert;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AlertServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AlertPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'alert' => Alert::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'alert',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'alert',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'alert',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'alert',
            path: 'roles/web'
        );
    }
}
