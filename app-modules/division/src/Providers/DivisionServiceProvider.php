<?php

namespace Assist\Division\Providers;

use Filament\Panel;
use Assist\Division\DivisionPlugin;
use Assist\Division\Models\Division;
use Illuminate\Support\ServiceProvider;
use Assist\Division\Observers\DivisionObserver;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class DivisionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new DivisionPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'division' => Division::class,
        ]);

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'division',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'division',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'division',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'division',
            path: 'roles/web'
        );
    }

    protected function registerObservers(): void
    {
        Division::observe(DivisionObserver::class);
    }
}
