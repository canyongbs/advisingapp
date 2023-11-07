<?php

namespace Assist\IntegrationAwsSesEventHandling\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\IntegrationAwsSesEventHandling\IntegrationAwsSesEventHandlingPlugin;

class IntegrationAwsSesEventHandlingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new IntegrationAwsSesEventHandlingPlugin()));
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
            module: 'integration-aws-ses-event-handling',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'integration-aws-ses-event-handling',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'integration-aws-ses-event-handling',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'integration-aws-ses-event-handling',
            path: 'roles/web'
        );
    }
}
