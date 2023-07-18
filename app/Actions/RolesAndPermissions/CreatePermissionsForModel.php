<?php

namespace App\Actions\RolesAndPermissions;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class CreatePermissionsForModel
{
    public function handle(string $model): void
    {
        $model = resolve($model);

        $this->createWebPermissionsForModel($model);
        $this->createApiPermissionsForModel($model);
    }

    protected function createWebPermissionsForModel(Model $model): void
    {
        if (method_exists($model, 'getWebPermissions')) {
            $model->getWebPermissions()->each(function ($permission) use ($model) {
                Permission::firstOrCreate([
                    'name' => Str::of($model->getMorphClass())->append('.')->append($permission),
                    'guard_name' => 'web',
                ]);
            });
        }
    }

    protected function createApiPermissionsForModel(Model $model): void
    {
        if (method_exists($model, 'getApiPermissions')) {
            $model->getApiPermissions()->each(function ($permission) use ($model) {
                Permission::firstOrCreate([
                    'name' => Str::of($model->getMorphClass())->append('.')->append($permission),
                    'guard_name' => 'api',
                ]);
            });
        }
    }
}
