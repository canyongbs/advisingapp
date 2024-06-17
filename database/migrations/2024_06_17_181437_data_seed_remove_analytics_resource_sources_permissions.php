<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'analytics_resource_source.view-any' => 'Analytics Resource Source',
        'analytics_resource_source.create' => 'Analytics Resource Source',
        'analytics_resource_source.*.view' => 'Analytics Resource Source',
        'analytics_resource_source.*.update' => 'Analytics Resource Source',
        'analytics_resource_source.*.delete' => 'Analytics Resource Source',
        'analytics_resource_source.*.restore' => 'Analytics Resource Source',
        'analytics_resource_source.*.force-delete' => 'Analytics Resource Source',
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
