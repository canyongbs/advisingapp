<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class extends Migration
{
    use CanModifyPermissions;

    private array $permissions = [
        'report-library.view-any' => 'Report Library',
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
        $this->deletePermissions(array_keys($this->permissions), $this->guards);
    }
};
