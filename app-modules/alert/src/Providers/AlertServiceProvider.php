<?php

namespace Assist\Alert\Providers;

use Filament\Panel;
use Assist\Alert\AlertPlugin;
use Assist\Alert\Models\Alert;
use Assist\Alert\Events\AlertCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Alert\Observers\AlertObserver;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Alert\Listeners\NotifySubscribersOfAlertCreated;

class AlertServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AlertPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'alert' => Alert::class,
        ]);

        $this->registerRolesAndPermissions();

        $this->registerObservers();

        $this->registerEvents();
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

    protected function registerObservers(): void
    {
        Alert::observe(AlertObserver::class);
    }

    protected function registerEvents(): void
    {
        Event::listen(
            AlertCreated::class,
            NotifySubscribersOfAlertCreated::class
        );
    }
}
