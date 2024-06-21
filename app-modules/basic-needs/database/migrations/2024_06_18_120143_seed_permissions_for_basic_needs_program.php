<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'basic_needs_program.view-any' => 'Basic Needs Program',
        'basic_needs_program.create' => 'Basic Needs Program',
        'basic_needs_program.*.view' => 'Basic Needs Program',
        'basic_needs_program.*.update' => 'Basic Needs Program',
        'basic_needs_program.*.delete' => 'Basic Needs Program',
        'basic_needs_program.*.restore' => 'Basic Needs Program',
        'basic_needs_program.*.force-delete' => 'Basic Needs Program',
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
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }
};
