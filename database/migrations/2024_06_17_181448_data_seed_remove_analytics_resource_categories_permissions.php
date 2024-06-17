<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'analytics_resource_category.view-any' => 'Analytics Resource Category',
        'analytics_resource_category.create' => 'Analytics Resource Category',
        'analytics_resource_category.*.view' => 'Analytics Resource Category',
        'analytics_resource_category.*.update' => 'Analytics Resource Category',
        'analytics_resource_category.*.delete' => 'Analytics Resource Category',
        'analytics_resource_category.*.restore' => 'Analytics Resource Category',
        'analytics_resource_category.*.force-delete' => 'Analytics Resource Category',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
