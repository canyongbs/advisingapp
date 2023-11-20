<?php

namespace Assist\Theme\Providers;

use Filament\Panel;
use Assist\Theme\ThemePlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ThemePlugin()));
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap(
            [],
        );

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'theme',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'theme',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'theme',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'theme',
            path: 'roles/web'
        );
    }
}
