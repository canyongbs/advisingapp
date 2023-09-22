<?php

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
