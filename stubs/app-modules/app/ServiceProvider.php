<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use StubModuleNamespace\StubClassNamePrefix\StubClassNamePrefixPlugin;

class StubClassNamePrefixServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new StubClassNamePrefixPlugin()));
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
            module: 'StubModuleName',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'StubModuleName',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'StubModuleName',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'StubModuleName',
            path: 'roles/web'
        );
    }
}
