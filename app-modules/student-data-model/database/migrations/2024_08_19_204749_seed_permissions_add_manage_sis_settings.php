<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    private array $permissions = [
        'sis.manage_sis_settings' => 'Student Information System',
    ];

    private array $guards = [
        'web',
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
