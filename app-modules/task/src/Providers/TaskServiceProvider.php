<?php

namespace Assist\Task\Providers;

use Filament\Panel;
use Assist\Task\TaskPlugin;
use Assist\Task\Models\Task;
use Assist\Task\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class TaskServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new TaskPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap(
            [
                'task' => Task::class,
            ]
        );

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'task',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'task',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'task',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'task',
            path: 'roles/web'
        );
    }

    protected function registerObservers(): void
    {
        Task::observe(TaskObserver::class);
    }
}
