<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'basic_need_category.view-any' => 'Basic Need Category',
        'basic_need_category.create' => 'Basic Need Category',
        'basic_need_category.*.view' => 'Basic Need Category',
        'basic_need_category.*.update' => 'Basic Need Category',
        'basic_need_category.*.delete' => 'Basic Need Category',
        'basic_need_category.*.restore' => 'Basic Need Category',
        'basic_need_category.*.force-delete' => 'Basic Need Category',
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
        $this->deletePermissions(array_keys($this->permissions), $this->guards);
    }
};
