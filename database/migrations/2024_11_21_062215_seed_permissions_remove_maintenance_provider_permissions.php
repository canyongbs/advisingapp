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
        'maintenance_provider.*.delete' => 'Maintenance Provider',
        'maintenance_provider.*.force-delete' => 'Maintenance Provider',
        'maintenance_provider.*.restore' => 'Maintenance Provider',
        'maintenance_provider.*.update' => 'Maintenance Provider',
        'maintenance_provider.*.view' => 'Maintenance Provider',
        'maintenance_provider.create' => 'Maintenance Provider',
        'maintenance_provider.view-any' => 'Maintenance Provider',
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
                'Maintenance Provider'
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
