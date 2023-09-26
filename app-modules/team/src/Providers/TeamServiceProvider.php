<?php

namespace Assist\Team\Providers;

use Filament\Panel;
use Assist\Team\TeamPlugin;
use Assist\Team\Models\Team;
use Assist\Team\Observers\TeamObserver;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class TeamServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new TeamPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'team' => Team::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'team',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'team',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'team',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'team',
            path: 'roles/web'
        );
    }
}
