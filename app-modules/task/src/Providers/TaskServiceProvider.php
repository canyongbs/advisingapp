<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Task\Providers;

use Filament\Panel;
use Assist\Task\TaskPlugin;
use Assist\Task\Models\Task;
use Filament\Support\Assets\Js;
use Assist\Task\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
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

        $this->registerAssets();
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

    protected function registerAssets(): void
    {
        FilamentAsset::register([Js::make('kanban', __DIR__ . '/../../resources/js/kanban.js')->loadedOnRequest()], 'canyon-gbs/task');
    }
}
