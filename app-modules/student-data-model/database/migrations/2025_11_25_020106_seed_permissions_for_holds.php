<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /** @var array<string> */
    private array $permissions = [
        'hold.view-any' => 'Hold',
        'hold.create' => 'Hold',
        'hold.*.view' => 'Hold',
        'hold.*.update' => 'Hold',
        'hold.*.delete' => 'Hold',
        'hold.*.restore' => 'Hold',
        'hold.*.force-delete' => 'Hold',
        'hold.import' => 'Hold',
    ];

    /** @var array<string> */
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
