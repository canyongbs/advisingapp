<?php

namespace Assist\MeetingCenter\Providers;

use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Observers\CalendarEventObserver;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\MeetingCenter\MeetingCenterPlugin;

class MeetingCenterServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new MeetingCenterPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([]);

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'meeting-center',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'meeting-center',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'meeting-center',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'meeting-center',
            path: 'roles/web'
        );
    }

    protected function registerObservers()
    {
        CalendarEvent::observe(CalendarEventObserver::class);
    }
}
