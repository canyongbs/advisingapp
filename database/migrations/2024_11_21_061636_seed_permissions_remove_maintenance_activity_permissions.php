<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    private array $permissions = [
        'maintenance_activity.*.delete' => 'Maintenance Activity',
        'maintenance_activity.*.force-delete' => 'Maintenance Activity',
        'maintenance_activity.*.restore' => 'Maintenance Activity',
        'maintenance_activity.*.update' => 'Maintenance Activity',
        'maintenance_activity.*.view' => 'Maintenance Activity',
        'maintenance_activity.create' => 'Maintenance Activity',
        'maintenance_activity.view-any' => 'Maintenance Activity',
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

        DB::table('permission_groups')
            ->whereIn('name', [
               'Maintenance Activity'
            ])
            ->delete();
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
