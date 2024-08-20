<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'theme.manage_college_brand_settings' => 'Manage College Brand Settings',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        foreach ($this->guards as $guard) {
            $this->createPermissions($this->permissions, $guard);
        }
    }

    public function down(): void
    {
        foreach ($this->guards as $guard) {
            $this->deletePermissions(array_keys($this->permissions), $guard);
        }
    }
};
