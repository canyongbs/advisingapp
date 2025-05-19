<?php

use Illuminate\Database\Migrations\Migration;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;

return new class extends Migration
{
    use CanModifyPermissions;

    /** @var array<string, string>*/
    private array $permissions = [
        'research_advisors.view-any' => 'Research Advisors',
        'research_advisors.create' => 'Research Advisors',
        'research_advisors.*.view' => 'Research Advisors',
        'research_advisors.*.update' => 'Research Advisors',
        'research_advisors.*.delete' => 'Research Advisors',
        'research_advisors.*.restore' => 'Research Advisors',
        'research_advisors.*.force-delete' => 'Research Advisors',
    ];
    /** @var array<string>*/
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
