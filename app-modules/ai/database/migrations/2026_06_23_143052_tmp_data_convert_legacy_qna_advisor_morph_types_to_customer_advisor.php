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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    protected array $morphColumns = [
        ['audits', 'auditable_type'],
        ['media', 'model_type'],
        ['open_ai_vector_stores', 'file_type'],
        ['open_ai_vector_stores', 'context_type'],
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $morphReplacements = [
                'qna_advisor' => 'customer_advisor',
                'qna_advisor_category' => 'customer_advisor_category',
                'qna_advisor_file' => 'customer_advisor_file',
                'qna_advisor_link' => 'customer_advisor_link',
                'qna_advisor_question' => 'customer_advisor_question',
            ];

            foreach ($this->morphColumns as [$table, $column]) {
                foreach ($morphReplacements as $old => $new) {
                    DB::table($table)
                        ->where($column, $old)
                        ->update([$column => $new]);
                }
            }

            $this->clearUniqueLocks();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $morphReplacements = [
                'customer_advisor' => 'qna_advisor',
                'customer_advisor_category' => 'qna_advisor_category',
                'customer_advisor_file' => 'qna_advisor_file',
                'customer_advisor_link' => 'qna_advisor_link',
                'customer_advisor_question' => 'qna_advisor_question',
            ];

            foreach ($this->morphColumns as [$table, $column]) {
                foreach ($morphReplacements as $old => $new) {
                    DB::table($table)
                        ->where($column, $old)
                        ->update([$column => $new]);
                }
            }
        });
    }

    protected function clearUniqueLocks(): void
    {
        $lockConfigs = [
            [
                'table' => 'customer_advisors',
                'key_column' => 'id',
                'job_class' => 'AdvisingApp\IntegrationOpenAi\Jobs\UploadCustomerAdvisorFilesToVectorStore',
            ],
            [
                'table' => 'customer_advisor_files',
                'key_column' => 'id',
                'job_class' => 'AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults',
            ],
            [
                'table' => 'customer_advisor_links',
                'key_column' => 'id',
                'job_class' => 'AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorLinkParsingResults',
            ],
        ];

        foreach ($lockConfigs as $config) {
            DB::table($config['table'])
                ->select($config['key_column'])
                ->chunkById(100, function ($records) use ($config) {
                    foreach ($records as $record) {
                        $lockKey = "laravel_unique_job:{$config['job_class']}:{$record->id}";

                        Cache::lock($lockKey)->forceRelease();
                    }
                });
        }
    }
};
