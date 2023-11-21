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

namespace Assist\MeetingCenter\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\MeetingCenter\Models\Calendar;
use Illuminate\Console\Scheduling\Schedule;
use Assist\MeetingCenter\Jobs\SyncCalendars;
use Assist\MeetingCenter\MeetingCenterPlugin;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\MeetingCenter\Observers\CalendarEventObserver;

class MeetingCenterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new MeetingCenterPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'calendar' => Calendar::class,
            'calendar_event' => CalendarEvent::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // TODO Ensure we are locking entities that have already been picked up for processing to avoid overlap
            $schedule->job(SyncCalendars::class)
                ->everyMinute()
                ->withoutOverlapping();
        });

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    protected function registerRolesAndPermissions(): void
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

    protected function registerObservers(): void
    {
        CalendarEvent::observe(CalendarEventObserver::class);
    }
}
