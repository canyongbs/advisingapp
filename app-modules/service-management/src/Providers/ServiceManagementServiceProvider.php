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

namespace Assist\ServiceManagement\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\ServiceManagement\ServiceManagementPlugin;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Observers\ServiceRequestObserver;
use Assist\ServiceManagement\Observers\ServiceRequestUpdateObserver;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use Assist\ServiceManagement\Services\ServiceRequestNumber\SqidPlusSixServiceRequestNumberGenerator;

class ServiceManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ServiceManagementPlugin()));

        $this->app->bind(ServiceRequestNumberGenerator::class, SqidPlusSixServiceRequestNumberGenerator::class);
    }

    public function boot(): void
    {
        Relation::morphMap([
            'service_request' => ServiceRequest::class,
            'service_request_priority' => ServiceRequestPriority::class,
            'service_request_status' => ServiceRequestStatus::class,
            'service_request_type' => ServiceRequestType::class,
            'service_request_update' => ServiceRequestUpdate::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
    }

    protected function registerObservers(): void
    {
        ServiceRequest::observe(ServiceRequestObserver::class);
        ServiceRequestUpdate::observe(ServiceRequestUpdateObserver::class);
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'service-management',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'service-management',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'service-management',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'service-management',
            path: 'roles/web'
        );
    }
}
