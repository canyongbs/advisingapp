<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'enrollment.create' => 'Enrollment',
        'enrollment.*.update' => 'Enrollment',
        'enrollment.*.delete' => 'Enrollment',
        'enrollment.*.restore' => 'Enrollment',
        'enrollment.*.force-delete' => 'Enrollment',
        'enrollment.import' => 'Enrollment',
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
