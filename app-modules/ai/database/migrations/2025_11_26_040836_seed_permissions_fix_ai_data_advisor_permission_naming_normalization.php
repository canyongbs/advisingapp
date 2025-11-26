<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /** @var array<string, string> */
    private array $permissions = [
        'ai-data-advisors.view-any' => 'data_advisor.view-any',
        'ai-data-advisors.*.view' => 'data_advisor.*.view',
        'ai-data-advisors.create' => 'data_advisor.create',
        'ai-data-advisors.*.update' => 'data_advisor.*.update',
        'ai-data-advisors.*.delete' => 'data_advisor.*.delete',
        'ai-data-advisors.*.restore' => 'data_advisor.*.restore',
        'ai-data-advisors.*.force-delete' => 'data_advisor.*.force-delete',
    ];

    /** @var array<string> */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->permissions, $guard);
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->permissions), $guard);
            });
        });
    }
};
