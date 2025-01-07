<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'program.create' => 'Program',
        'program.*.update' => 'Program',
        'program.*.delete' => 'Program',
        'program.*.restore' => 'Program',
        'program.*.force-delete' => 'Program',
        'program.import' => 'Program',
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
