<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'student.create' => 'Student',
        'student.*.update' => 'Student',
        'student.*.delete' => 'Student',
        'student.*.restore' => 'Student',
        'student.*.force-delete' => 'Student',
        'student.import' => 'Student',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions($this->permissions, $guard);
            });
    }
};
