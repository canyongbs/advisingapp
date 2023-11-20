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

namespace Assist\Authorization\Actions;

use ReflectionClass;
use Assist\Authorization\Models\Permission;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

class CreatePermissions
{
    public function handle(): void
    {
        // Model related permissions
        $this->createModelPermissions();

        // Non-model related permissions
        $this->createCustomPermissions();
    }

    protected function createModelPermissions(): void
    {
        $morphMap = Relation::morphMap();

        collect($morphMap)
            ->filter(function ($modelClass) {
                $implementsPermissions = false;

                $reflection = new ReflectionClass($modelClass);
                $parentClass = $reflection->getParentClass();

                if (in_array(DefinesPermissions::class, $reflection->getTraitNames()) || in_array(DefinesPermissions::class, $parentClass->getTraitNames())) {
                    $implementsPermissions = true;
                }

                return $implementsPermissions;
            })->each(function ($modelClass) {
                resolve(CreatePermissionsForModel::class)->handle($modelClass);
            });
    }

    // TODO Document the impact of what this concept of registering permissions actually means
    // We do this in order to avoid any potential naming collisions, as module names should be unique
    // So, when we register custom permissions, we prefix the module name to the custom permission
    // This needs to be very clear so anyone implementing these permissions in their application is aware of how to use the permissions
    // But ultimately, we are probably going to need to introduce a way for third party plugins to register
    // And migrate their permissions separately from this initial seeding process.
    protected function createCustomPermissions(): void
    {
        $registry = resolve(AuthorizationPermissionRegistry::class);

        foreach ($registry->getModuleWebPermissions() as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$permission}",
                    'guard_name' => 'web',
                ]);
            }
        }

        foreach ($registry->getModuleApiPermissions() as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$permission}",
                    'guard_name' => 'api',
                ]);
            }
        }
    }
}
