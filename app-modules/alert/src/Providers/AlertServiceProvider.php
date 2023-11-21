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
