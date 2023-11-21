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

namespace Assist\Authorization\Console\Commands;

use Illuminate\Console\Command;
use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;
use App\Actions\Finders\ApplicationModules;
use Assist\Authorization\Models\Permission;

class SyncRolesAndPermissions extends Command
{
    protected $signature = 'roles-and-permissions:sync';

    protected $description = 'This command will sync all roles and permissions defined in the roles and permissions config files.';

    public function handle(): int
    {
        // TODO Put handling in place to prevent this from being run in production IF it has already been run once
        // We are going to introduce a convention for "one-time" operations similar to Laravel migrations in order to handle this

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Seed roles and permissions
        Artisan::call(SetupRoles::class);
        Artisan::call(SetupPermissions::class);

        $this->syncWebPermissions();

        $this->syncApiPermissions();

        // TODO We might just leave this command out for now, and just allow for manual creation of role groups per org
        // Artisan::call(SetupRoleGroups::class);

        return self::SUCCESS;
    }

    protected function syncWebPermissions(): void
    {
        Role::where('guard_name', 'web')
            ->where('name', '!=', 'authorization.super_admin')
            ->cursor()
            ->each(function (Role $role) {
                $this->syncPermissionFor('web', $role);
            });
    }

    protected function syncApiPermissions(): void
    {
        Role::where('guard_name', 'api')
            ->where('name', '!=', 'authorization.super_admin')
            ->cursor()
            ->each(function (Role $role) {
                $this->syncPermissionFor('api', $role);
            });
    }

    protected function syncPermissionFor(string $guard, Role $role): void
    {
        // This is assuming that our roles are named in the following convention
        // {module}.{role}
        [$module, $roleFileName] = explode('.', $role->name);

        $permissions = resolve(ApplicationModules::class)
            ->moduleConfig(
                module: $module,
                path: "roles/{$guard}/{$roleFileName}"
            );

        collect($permissions)->each(function ($specificPermissions, $permissionConvention) use ($role, $guard) {
            if (blank($specificPermissions)) {
                return;
            }

            collect($specificPermissions)
                ->each(function ($specificPermission, $resource) use ($role, $guard) {
                    if (! is_array($specificPermission)) {
                        $this->syncCustomPermissions($role, $specificPermission, $guard);
                    } else {
                        $this->syncModelPermissions($role, $resource, $specificPermission, $guard);
                    }
                });
        });
    }

    protected function syncCustomPermissions(Role $role, string $specificPermission, string $permissionType): void
    {
        $foundPermissions = Permission::firstWhere([
            'name' => $specificPermission,
            'guard_name' => $permissionType,
        ])->name;

        $role->syncPermissions([$role->permissions, $foundPermissions]);
    }

    protected function syncModelPermissions(Role $role, string $resource, array $specificPermission, string $permissionType): void
    {
        if (count($specificPermission) === 1 && $specificPermission[0] === '*') {
            $foundPermissions = Permission::where('name', 'like', "{$resource}.%")
                ->where('guard_name', $permissionType)
                ->pluck('name');
        } else {
            $foundPermissions = collect($specificPermission)->map(function ($permission) use ($resource, $permissionType) {
                return Permission::firstWhere([
                    'name' => "{$resource}.{$permission}",
                    'guard_name' => $permissionType,
                ])->name;
            });
        }

        $role->syncPermissions([$role->permissions, $foundPermissions]);
    }
}
