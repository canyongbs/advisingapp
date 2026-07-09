<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
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
        DB::transaction(function () {
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

            DB::table('settings')
                ->where('group', 'ai-qna-advisor')
                ->update(['group' => 'ai-customer-advisor']);

            DB::table('portal_authentications')
                ->where('portal_type', 'qna_advisor_widget')
                ->update(['portal_type' => 'customer_advisor_widget']);

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->permissions, $guard);
            });

            $this->renamePermissionGroups([
                'QnA Advisor' => 'Customer Advisor',
                'QnA Advisor Embed' => 'Customer Advisor Embed',
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
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

            DB::table('settings')
                ->where('group', 'ai-customer-advisor')
                ->update(['group' => 'ai-qna-advisor']);

            DB::table('portal_authentications')
                ->where('portal_type', 'customer_advisor_widget')
                ->update(['portal_type' => 'qna_advisor_widget']);

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
