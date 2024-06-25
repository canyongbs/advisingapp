<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'segment.view-any' => 'Segment',
        'segment.create' => 'Segment',
        'segment.*.view' => 'Segment',
        'segment.*.update' => 'Segment',
        'segment.*.delete' => 'Segment',
        'segment.*.restore' => 'Segment',
        'segment.*.force-delete' => 'Segment',
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
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }
};
