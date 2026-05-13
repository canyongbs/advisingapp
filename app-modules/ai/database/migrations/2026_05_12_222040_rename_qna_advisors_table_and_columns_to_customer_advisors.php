<?php

use App\Features\RenameQnaAdvisorsFeature;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'qna_advisor.*.delete' => 'customer_advisor.*.delete',
        'qna_advisor.*.force-delete' => 'customer_advisor.*.force-delete',
        'qna_advisor.*.restore' => 'customer_advisor.*.restore',
        'qna_advisor.*.update' => 'customer_advisor.*.update',
        'qna_advisor.*.view' => 'customer_advisor.*.view',
        'qna_advisor.create' => 'customer_advisor.create',
        'qna_advisor.view-any' => 'customer_advisor.view-any',
        'qna_advisor_embed.view-any' => 'customer_advisor_embed.view-any',
        'qna_advisor_embed.*.view' => 'customer_advisor_embed.*.view',
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
        DB::transaction(function() {
            Schema::rename('qna_advisor_categories', 'customer_advisor_categories');
            Schema::rename('qna_advisor_files', 'customer_advisor_files');
            Schema::rename('qna_advisor_links', 'customer_advisor_links');
            Schema::rename('qna_advisor_messages', 'customer_advisor_messages');
            Schema::rename('qna_advisor_questions', 'customer_advisor_questions');
            Schema::rename('qna_advisor_threads', 'customer_advisor_threads');
            Schema::rename('qna_advisors', 'customer_advisors');

            Schema::table('customer_advisor_categories', function (Blueprint $table) {
                $table->renameColumn('qna_advisor_id', 'customer_advisor_id');
            });

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->permissions, $guard);
            });

            $this->renamePermissionGroups([
                'QnA Advisor' => 'Customer Advisor',
                'QnA Advisor Embed' => 'Customer Advisor Embed',
            ]);

            RenameQnaAdvisorsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function() {
            RenameQnaAdvisorsFeature::deactivate();

            Schema::rename('customer_advisor_categories', 'qna_advisor_categories');
            Schema::rename('customer_advisor_files', 'qna_advisor_files');
            Schema::rename('customer_advisor_links', 'qna_advisor_links');
            Schema::rename('customer_advisor_messages', 'qna_advisor_messages');
            Schema::rename('customer_advisor_questions', 'qna_advisor_questions');
            Schema::rename('customer_advisor_threads', 'qna_advisor_threads');
            Schema::rename('customer_advisors', 'qna_advisors');

            Schema::table('qna_advisor_categories', function (Blueprint $table) {
                $table->renameColumn('customer_advisor_id', 'qna_advisor_id');
            });

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->permissions), $guard);
            });

            $this->renamePermissionGroups([
                'Customer Advisor' => 'QnA Advisor',
                'Customer Advisor Embed' => 'QnA Advisor Embed',
            ]);
        });
    }
};
