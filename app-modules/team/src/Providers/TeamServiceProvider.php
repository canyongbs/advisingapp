<?php

namespace Assist\Team\Providers;

use Filament\Panel;
use Assist\Team\TeamPlugin;
use Assist\Team\Models\Team;
use Assist\Team\Models\TeamUser;
use Illuminate\Support\ServiceProvider;
use Assist\Team\Observers\TeamUserObserver;
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

        $this->registerObservers();
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

    protected function registerObservers(): void
    {
        TeamUser::observe(TeamUserObserver::class);
    }
}
