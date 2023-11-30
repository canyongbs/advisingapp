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
