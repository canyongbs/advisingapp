<?php

use Illuminate\Database\Migrations\Migration;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;

return new class extends Migration
{
    use CanModifyPermissions;

    /** @var array<string, string> */
    private array $permissions = [
        'qna_advisor.view-any' => 'QnA Advisors',
        'qna_advisor.create' => 'QnA Advisors',
        'qna_advisor.*.view' => 'QnA Advisors',
        'qna_advisor.*.update' => 'QnA Advisors',
        'qna_advisor.*.delete' => 'QnA Advisors',
        'qna_advisor.*.restore' => 'QnA Advisors',
        'qna_advisor.*.force-delete' => 'QnA Advisors',
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
