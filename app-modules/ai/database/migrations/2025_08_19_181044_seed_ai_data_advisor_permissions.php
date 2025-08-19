<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use CanModifyPermissions;

    /** @var array<string, string> */
    private array $permissions = [
        'ai-data-advisors.view-any' => 'Data Advisor',
        'ai-data-advisors.*.view' => 'Data Advisor',
        'ai-data-advisors.create' => 'Data Advisor',
        'ai-data-advisors.*.update' => 'Data Advisor',
        'ai-data-advisors.*.delete' => 'Data Advisor',
        'ai-data-advisors.*.restore' => 'Data Advisor',
        'ai-data-advisors.*.force-delete' => 'Data Advisor',
    ];

    /** @var array<string> */
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
