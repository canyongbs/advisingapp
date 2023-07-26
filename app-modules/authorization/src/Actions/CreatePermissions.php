<?php

namespace Assist\Authorization\Actions;

use App\Actions\Finders\ApplicationModels;
use Assist\Authorization\Models\Permission;

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

    // TODO This will need to be refactored.
    // Each "module" will need to register its models somewhere
    protected function createModelPermissions(): void
    {
        resolve(ApplicationModels::class)->implementingPermissions()->each(function ($modelClass) {
            resolve(CreatePermissionsForModel::class)->handle($modelClass);
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
