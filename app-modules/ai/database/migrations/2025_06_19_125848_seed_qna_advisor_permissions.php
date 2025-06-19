<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string> $permissions
     */
    private array $permissions = [
        'qna_advisor_embed.view-any' => 'QnA Advisor Embed',
        'qna_advisor_embed.*.view' => 'QnA Advisor Embed',

        'qna_advisor_category.view-any' => 'QnA Advisor Category',
        'qna_advisor_category.create' => 'QnA Advisor Category',
        'qna_advisor_category.*.view' => 'QnA Advisor Category',
        'qna_advisor_category.*.update' => 'QnA Advisor Category',
        'qna_advisor_category.*.delete' => 'QnA Advisor Category',
        'qna_advisor_category.*.restore' => 'QnA Advisor Category',
        'qna_advisor_category.*.force-delete' => 'QnA Advisor Category',

        'qna_advisor_question.view-any' => 'QnA Advisor Question',
        'qna_advisor_question.create' => 'QnA Advisor Question',
        'qna_advisor_question.*.view' => 'QnA Advisor Question',
        'qna_advisor_question.*.update' => 'QnA Advisor Question',
        'qna_advisor_question.*.delete' => 'QnA Advisor Question',
        'qna_advisor_question.*.restore' => 'QnA Advisor Question',
        'qna_advisor_question.*.force-delete' => 'QnA Advisor Question',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }
};

