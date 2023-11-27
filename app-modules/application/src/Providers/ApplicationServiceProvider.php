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

namespace Assist\Application\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Application\ApplicationPlugin;
use Assist\Application\Models\Application;
use Assist\Application\Models\ApplicationStep;
use Assist\Application\Models\ApplicationField;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\Application\Models\ApplicationSubmission;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Application\Models\ApplicationAuthentication;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Application\Observers\ApplicationSubmissionObserver;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ApplicationPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'application' => Application::class,
            'application_field' => ApplicationField::class,
            'application_submission' => ApplicationSubmission::class,
            'application_step' => ApplicationStep::class,
            'application_authentication' => ApplicationAuthentication::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
        $this->registerEvents();
    }

    public function registerObservers(): void
    {
        ApplicationSubmission::observe(ApplicationSubmissionObserver::class);
    }

    public function registerEvents(): void
    {
        //Event::listen(
        //    events: ApplicationSubmissionCreated::class,
        //    // TODO: Swap out for the correct listener
        //    listener: NotifySubscribersOfFormSubmission::class
        //);
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'application',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'application',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'application',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'application',
            path: 'roles/web'
        );
    }
}
