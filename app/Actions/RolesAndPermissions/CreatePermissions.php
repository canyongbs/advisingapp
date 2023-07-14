<?php

namespace App\Actions\RolesAndPermissions;

use Spatie\Permission\Models\Permission;

class CreatePermissions
{
    public function handle(): void
    {
        // Model related permissions
        $this->createModelPermissions();

        // Non-model related permissions
        $this->createCorePermissions();

        // Package related permissions
        // $this->createPackagePermissions();
    }

    protected function createModelPermissions(): void
    {
        get_application_models()->each(function ($modelClass) {
            $createPermissionsForModel = resolve(CreatePermissionsForModel::class);
            $createPermissionsForModel->handle($modelClass);
        });
    }

    protected function createCorePermissions(): void
    {
        foreach (config('permissions.web.core') as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (config('permissions.api.core') as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    protected function createPackagePermissions(): void
    {
    }
}
