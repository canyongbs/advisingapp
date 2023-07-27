<?php

namespace Assist\Authorization\Console\Commands;

use Illuminate\Console\Command;
use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;
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

        Artisan::call(SetupRoleGroups::class);

        return self::SUCCESS;
    }

    protected function syncWebPermissions(): void
    {
        // TODO We'll need to get the correct config path per module when we attempt to sync web permissions
        Role::where('guard_name', 'web')
            ->where('name', '!=', 'super_admin')
            ->get()
            ->each(function (Role $role) {
                $this->syncPermissionFor('web', $role, config("roles.web.{$role->name}"));
            });
    }

    protected function syncApiPermissions(): void
    {
        // TODO We'll need to get the correct config path per module when we attempt to sync api permissions
        Role::where('guard_name', 'api')
            ->where('name', '!=', 'super_admin')
            ->get()
            ->each(function (Role $role) {
                $this->syncPermissionFor('api', $role, config("roles.api.{$role->name}"));
            });
    }

    protected function syncPermissionFor(string $permissionType, Role $role, array $permissions): void
    {
        collect($permissions)->each(function ($specificPermissions, $permissionConvention) use ($role, $permissionType) {
            if (blank($specificPermissions)) {
                return;
            }

            collect($specificPermissions)
                ->each(function ($specificPermission, $resource) use ($role, $permissionType) {
                    if (! is_array($specificPermission)) {
                        $this->syncCustomPermissions($role, $specificPermission, $permissionType);
                    } else {
                        $this->syncModelPermissions($role, $resource, $specificPermission, $permissionType);
                    }
                });
        });
    }

    protected function syncCustomPermissions(Role $role, string $specificPermission, string $permissionType): void
    {
        $foundPermissions = Permission::firstWhere([
            'name' => $specificPermission,
            'guard_name' => $permissionType,
        ])->id;

        $role->syncPermissions([$role->permissions, $foundPermissions]);
    }

    protected function syncModelPermissions(Role $role, string $resource, array $specificPermission, string $permissionType): void
    {
        if (count($specificPermission) === 1 && $specificPermission[0] === '*') {
            $foundPermissions = Permission::where('name', 'like', "{$resource}.%")
                ->where('guard_name', $permissionType)
                ->pluck('id');
        } else {
            $foundPermissions = collect($specificPermission)->map(function ($permission) use ($resource, $permissionType) {
                return Permission::firstWhere([
                    'name' => "{$resource}.{$permission}",
                    'guard_name' => $permissionType,
                ])->id;
            });
        }

        $role->syncPermissions([$role->permissions, $foundPermissions]);
    }
}
