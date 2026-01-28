<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string> $permissions
     */
    private array $permissions = [
        'export_hub.view-any' => 'Export Hub',
        'export_hub.create' => 'Export Hub',
        'export_hub.*.view' => 'Export Hub',
        'export_hub.*.update' => 'Export Hub',
        'export_hub.*.delete' => 'Export Hub',
        'export_hub.*.restore' => 'Export Hub',
        'export_hub.*.force-delete' => 'Export Hub',
        'export_hub.import' => 'Export Hub',
    ];

    /**
     * @var array<string> $guards
     */
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
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }
};
