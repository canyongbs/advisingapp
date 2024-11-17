<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use CanModifyPermissions;

    private array $permissions = [
        'alert_status.*.delete' => 'Alert Status',
        'alert_status.*.force-delete' => 'Alert Status',
        'alert_status.*.restore' => 'Alert Status',
        'alert_status.*.update' => 'Alert Status',
        'alert_status.*.view' => 'Alert Status',
        'alert_status.create' => 'Alert Status',
        'alert_status.view-any' => 'Alert Status',
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
