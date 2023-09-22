<?php

namespace Assist\IntegrationGoogleAnalytics\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\IntegrationGoogleAnalytics\IntegrationGoogleAnalyticsPlugin;

class IntegrationGoogleAnalyticsServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new IntegrationGoogleAnalyticsPlugin()));
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
            module: 'integration-google-analytics',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'integration-google-analytics',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'integration-google-analytics',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'integration-google-analytics',
            path: 'roles/web'
        );
    }
}
