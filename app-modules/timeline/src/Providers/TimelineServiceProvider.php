<?php

namespace Assist\Timeline\Providers;

use Filament\Panel;
use Assist\Timeline\TimelinePlugin;
use Assist\Timeline\Models\Timeline;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Timeline\Listeners\AddRecordToTimeline;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\Timeline\Listeners\RemoveRecordFromTimeline;
use Assist\Authorization\AuthorizationPermissionRegistry;

class TimelineServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new TimelinePlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'timeline' => Timeline::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerEvents();
    }

    protected function registerEvents(): void
    {
        Event::listen(
            TimelineableRecordCreated::class,
            AddRecordToTimeline::class
        );

        Event::listen(
            TimelineableRecordDeleted::class,
            RemoveRecordFromTimeline::class
        );
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'timeline',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'timeline',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'timeline',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'timeline',
            path: 'roles/web'
        );
    }
}
