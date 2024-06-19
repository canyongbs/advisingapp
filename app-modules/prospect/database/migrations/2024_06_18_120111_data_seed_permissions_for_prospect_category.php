<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'prospect_category.view-any' => 'Prospect Category',
        'prospect_category.create' => 'Prospect Category',
        'prospect_category.*.view' => 'Prospect Category',
        'prospect_category.*.update' => 'Prospect Category',
        'prospect_category.*.delete' => 'Prospect Category',
        'prospect_category.*.restore' => 'Prospect Category',
        'prospect_category.*.force-delete' => 'Prospect Category',
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
