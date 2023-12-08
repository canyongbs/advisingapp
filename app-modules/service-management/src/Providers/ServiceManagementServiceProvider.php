<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\ServiceManagement\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\Authorization\AuthorizationRoleRegistry;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\ServiceManagement\ServiceManagementPlugin;
use AdvisingApp\ServiceManagement\Models\ServiceRequestType;
use AdvisingApp\Authorization\AuthorizationPermissionRegistry;
use AdvisingApp\ServiceManagement\Models\ServiceRequestStatus;
use AdvisingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AdvisingApp\ServiceManagement\Models\ServiceRequestHistory;
use AdvisingApp\ServiceManagement\Models\ServiceRequestPriority;
use AdvisingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestUpdateObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestHistoryObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestAssignmentObserver;
use AdvisingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use AdvisingApp\ServiceManagement\Services\ServiceRequestNumber\SqidPlusSixServiceRequestNumberGenerator;

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
            'service_request_assignment' => ServiceRequestAssignment::class,
            'service_request_history' => ServiceRequestHistory::class,
            'service_request_priority' => ServiceRequestPriority::class,
            'service_request_status' => ServiceRequestStatus::class,
            'service_request_type' => ServiceRequestType::class,
            'service_request_update' => ServiceRequestUpdate::class,
            'service_request' => ServiceRequest::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
    }

    protected function registerObservers(): void
    {
        ServiceRequest::observe(ServiceRequestObserver::class);
        ServiceRequestUpdate::observe(ServiceRequestUpdateObserver::class);
        ServiceRequestAssignment::observe(ServiceRequestAssignmentObserver::class);
        ServiceRequestHistory::observe(ServiceRequestHistoryObserver::class);
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
