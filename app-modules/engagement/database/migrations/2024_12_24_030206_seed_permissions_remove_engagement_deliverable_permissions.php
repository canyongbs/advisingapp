<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'engagement_deliverable.*.delete' => 'Engagement Deliverable',
        'engagement_deliverable.*.force-delete' => 'Engagement Deliverable',
        'engagement_deliverable.*.restore' => 'Engagement Deliverable',
        'engagement_deliverable.*.update' => 'Engagement Deliverable',
        'engagement_deliverable.*.view' => 'Engagement Deliverable',
        'engagement_deliverable.create' => 'Engagement Deliverable',
        'engagement_deliverable.view-any' => 'Engagement Deliverable',
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
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
