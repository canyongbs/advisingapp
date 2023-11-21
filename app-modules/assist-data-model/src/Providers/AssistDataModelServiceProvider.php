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

namespace Assist\AssistDataModel\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\AssistDataModel\Models\Program;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Models\Enrollment;
use Assist\AssistDataModel\Models\Performance;
use Assist\AssistDataModel\AssistDataModelPlugin;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AssistDataModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistDataModelPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'student' => Student::class,
            'enrollment' => Enrollment::class,
            'performance' => Performance::class,
            'program' => Program::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'assist-data-model',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'assist-data-model',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'assist-data-model',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'assist-data-model',
            path: 'roles/web'
        );
    }
}
