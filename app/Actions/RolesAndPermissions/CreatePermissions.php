<?php

namespace App\Actions\RolesAndPermissions;

use Spatie\Permission\Models\Permission;
use App\Actions\Finders\ApplicationModels;

class CreatePermissions
{
    public function handle(): void
    {
        // Model related permissions
        $this->createModelPermissions();

        // Non-model related permissions
        $this->createCustomPermissions();

        // TODO Package related permissions
        // $this->createPackagePermissions();
    }

    protected function createModelPermissions(): void
    {
        resolve(ApplicationModels::class)->implementingPermissions()->each(function ($modelClass) {
            $createPermissionsForModel = resolve(CreatePermissionsForModel::class);
            $createPermissionsForModel->handle($modelClass);
        });
    }

    protected function createCustomPermissions(): void
    {
        foreach (config('permissions.web.custom') as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (config('permissions.api.custom') as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }
    }

    // TODO
    protected function createPackagePermissions(): void
    {
    }
}
