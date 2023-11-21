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
