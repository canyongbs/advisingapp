<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use CanModifyPermissions;

    /** @var array<string> $permissions */
    private array $permissions = [
        'support_category.view-any' => 'Support Category',
        'support_category.create' => 'Support Category',
        'support_category.*.view' => 'Support Category',
        'support_category.*.update' => 'Support Category',
        'support_category.*.delete' => 'Support Category',
        'support_category.*.restore' => 'Support Category',
        'support_category.*.force-delete' => 'Support Category',
    ];

    /** @var array<string> $guards */
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
