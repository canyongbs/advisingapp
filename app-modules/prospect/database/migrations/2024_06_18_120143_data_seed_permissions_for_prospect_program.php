<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'prospect_program.view-any' => 'Prospect Program',
        'prospect_program.create' => 'Prospect Program',
        'prospect_program.*.view' => 'Prospect Program',
        'prospect_program.*.update' => 'Prospect Program',
        'prospect_program.*.delete' => 'Prospect Program',
        'prospect_program.*.restore' => 'Prospect Program',
        'prospect_program.*.force-delete' => 'Prospect Program',
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
