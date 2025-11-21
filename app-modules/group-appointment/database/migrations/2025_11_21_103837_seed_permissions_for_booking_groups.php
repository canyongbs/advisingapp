<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'booking_group.*.delete' => 'Booking Group',
        'booking_group.*.force-delete' => 'Booking Group',
        'booking_group.*.restore' => 'Booking Group',
        'booking_group.*.update' => 'Booking Group',
        'booking_group.*.view' => 'Booking Group',
        'booking_group.create' => 'Booking Group',
        'booking_group.view-any' => 'Booking Group',
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
