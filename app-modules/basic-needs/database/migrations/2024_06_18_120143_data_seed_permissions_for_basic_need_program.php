<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'basic_need_program.view-any' => 'Basic Need Program',
        'basic_need_program.create' => 'Basic Need Program',
        'basic_need_program.*.view' => 'Basic Need Program',
        'basic_need_program.*.update' => 'Basic Need Program',
        'basic_need_program.*.delete' => 'Basic Need Program',
        'basic_need_program.*.restore' => 'Basic Need Program',
        'basic_need_program.*.force-delete' => 'Basic Need Program',
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
