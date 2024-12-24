<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'saas_global_admin.view-any' => 'Global Admin',
        'saas_global_admin.create' => 'Global Admin',
        'saas_global_admin.*.view' => 'Global Admin',
        'saas_global_admin.*.update' => 'Global Admin',
        'saas_global_admin.*.delete' => 'Global Admin',
        'saas_global_admin.*.restore' => 'Global Admin',
        'saas_global_admin.*.force-delete' => 'Global Admin',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
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

    public function down(): void
    {
        collect($this->guards)
            ->each(fn(string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }
};
