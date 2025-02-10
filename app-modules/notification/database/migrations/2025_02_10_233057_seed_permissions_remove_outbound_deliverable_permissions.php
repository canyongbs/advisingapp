<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'outbound_deliverable.*.delete' => 'Outbound Deliverable',
        'outbound_deliverable.*.force-delete' => 'Outbound Deliverable',
        'outbound_deliverable.*.restore' => 'Outbound Deliverable',
        'outbound_deliverable.*.update' => 'Outbound Deliverable',
        'outbound_deliverable.*.view' => 'Outbound Deliverable',
        'outbound_deliverable.create' => 'Outbound Deliverable',
        'outbound_deliverable.view-any' => 'Outbound Deliverable',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
