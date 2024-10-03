<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use CanModifyPermissions;

    private array $permissions = [
        'student_record_manager.view-any' => 'Student Record Manager',
        'student_record_manager.create' => 'Student Record Manager',
        'student_record_manager.*.view' => 'Student Record Manager',
        'student_record_manager.*.update' => 'Student Record Manager',
        'student_record_manager.*.delete' => 'Student Record Manager',
        'student_record_manager.*.restore' => 'Student Record Manager',
        'student_record_manager.*.force-delete' => 'Student Record Manager',
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
