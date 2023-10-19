<?php

namespace Assist\CareTeam\Providers;

use Filament\Panel;
use Assist\CareTeam\CareTeamPlugin;
use Assist\CareTeam\Models\CareTeam;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class CareTeamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CareTeamPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'create_team' => CareTeam::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'care-team',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'care-team',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'care-team',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'care-team',
            path: 'roles/web'
        );
    }
}
