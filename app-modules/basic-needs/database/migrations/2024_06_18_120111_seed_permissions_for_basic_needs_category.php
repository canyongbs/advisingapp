<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'basic_needs_category.view-any' => 'Basic Needs Category',
        'basic_needs_category.create' => 'Basic Needs Category',
        'basic_needs_category.*.view' => 'Basic Needs Category',
        'basic_needs_category.*.update' => 'Basic Needs Category',
        'basic_needs_category.*.delete' => 'Basic Needs Category',
        'basic_needs_category.*.restore' => 'Basic Needs Category',
        'basic_needs_category.*.force-delete' => 'Basic Needs Category',
    ];

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
