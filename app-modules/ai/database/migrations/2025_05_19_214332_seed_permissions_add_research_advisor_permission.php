<?php

use Illuminate\Database\Migrations\Migration;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;

return new class extends Migration
{
    use CanModifyPermissions;

    /** @var array<string, string>*/
    private array $permissions = [
        'research_advisors.view-any' => 'Research Advisor',
        'research_advisors.create' => 'Research Advisor',
        'research_advisors.*.view' => 'Research Advisor',
        'research_advisors.*.update' => 'Research Advisor',
        'research_advisors.*.delete' => 'Research Advisor',
        'research_advisors.*.restore' => 'Research Advisor',
        'research_advisors.*.force-delete' => 'Research Advisor',
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
