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

use App\Models\Media;
use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingDoesNotExist;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends SettingsMigration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'research_advisor.view-any' => 'Research Advisor',
        'research_advisor.*.view' => 'Research Advisor',
        'data_advisor.view-any' => 'Data Advisor',
        'data_advisor.create' => 'Data Advisor',
        'data_advisor.*.view' => 'Data Advisor',
        'data_advisor.*.update' => 'Data Advisor',
        'data_advisor.*.delete' => 'Data Advisor',
        'data_advisor.*.restore' => 'Data Advisor',
        'data_advisor.*.force-delete' => 'Data Advisor',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
        'api',
    ];

    /**
     * @var list<string>
     */
    private array $applicableFeatureKeys = [
        'ai.open_ai_gpt_4o_applicable_features',
        'ai.open_ai_gpt_4o_mini_applicable_features',
        'ai.open_ai_gpt_o3_applicable_features',
        'ai.open_ai_gpt_41_mini_applicable_features',
        'ai.open_ai_gpt_41_nano_applicable_features',
        'ai.open_ai_gpt_o4_mini_applicable_features',
        'ai.open_ai_gpt_5_applicable_features',
        'ai.open_ai_gpt_5_mini_applicable_features',
        'ai.open_ai_gpt_54_mini_applicable_features',
        'ai.open_ai_gpt_5_nano_applicable_features',
        'ai.open_ai_gpt_54_nano_applicable_features',
        'ai.open_ai_gpt_56_luna_applicable_features',
        'ai.jina_deepsearch_v1_applicable_features',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            collect($this->guards)
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            DB::table('permission_groups')
                ->whereIn('name', array_values(array_unique($this->permissions)))
                ->delete();

            $this->migrator->deleteIfExists('ai_research_assistant.discovery_model');
            $this->migrator->deleteIfExists('ai_research_assistant.research_model');
            $this->migrator->deleteIfExists('ai_research_assistant.context');
            $this->migrator->deleteIfExists('ai_research_assistant.reasoning_effort');

            foreach ($this->applicableFeatureKeys as $key) {
                try {
                    $this->migrator->update(
                        $key,
                        fn (array $values): array => array_values(array_diff($values, ['research_advisor'])),
                    );
                } catch (SettingDoesNotExist $exception) {
                    // Not every model has applicable features settings.
                }
            }

            Schema::drop('open_ai_research_request_vector_stores');
            Schema::drop('research_request_questions');
            Schema::drop('research_request_parsed_files');
            Schema::drop('research_request_parsed_links');
            Schema::drop('research_request_parsed_search_results');
            Schema::drop('research_requests');
            Schema::drop('research_request_folders');
            Schema::drop('data_advisors');

            DB::table('audits')->where('auditable_type', 'data_advisor')->delete();

            DB::table('report_user_accesses')->where('report_key', 'research-advisor-report')->delete();
            DB::table('report_team_accesses')->where('report_key', 'research-advisor-report')->delete();

            DB::table('exports')
                ->where('exporter', 'AdvisingApp\\Report\\Filament\\Exports\\ResearchAdvisorExporter')
                ->delete();

            // Last, so a failure above rolls back before any files are removed from storage.
            Media::query()
                ->where('model_type', 'research_request')
                ->lazyById()
                ->each(fn (Media $media) => $media->delete());
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            collect($this->guards)
                ->each(function (string $guard) {
                    $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->all());

                    $this->createPermissions($permissions, $guard);
                });

            $this->migrator->add('ai_research_assistant.discovery_model', null);
            $this->migrator->add('ai_research_assistant.research_model', null);
            $this->migrator->add('ai_research_assistant.context', null);
            $this->migrator->add('ai_research_assistant.reasoning_effort', 'high');

            Schema::create('data_advisors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('research_request_folders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('research_requests', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title')->nullable();
                $table->text('topic');
                $table->longText('results')->nullable();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreignUuid('folder_id')->nullable()->constrained('research_request_folders')->nullOnDelete();
                $table->jsonb('links')->nullable();
                $table->string('research_model')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->jsonb('search_queries')->nullable();
                $table->jsonb('outline')->nullable();
                $table->jsonb('remaining_outline')->nullable();
                $table->jsonb('sources')->nullable();
            });

            Schema::create('research_request_questions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->text('content');
                $table->text('response')->nullable();
                $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('research_request_parsed_files', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
                $table->dateTime('uploaded_at');
                $table->longText('results');
                $table->foreignId('media_id')->constrained()->cascadeOnDelete();
                $table->string('file_id');
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('research_request_parsed_links', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
                $table->longText('results');
                $table->text('url');
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('research_request_parsed_search_results', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
                $table->longText('results');
                $table->text('search_query');
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('open_ai_research_request_vector_stores', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('research_request_id');
                $table->text('deployment_hash');
                $table->dateTime('ready_until')->nullable();
                $table->string('vector_store_id')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('research_request_id', 'open_ai_rr_vs_research_request_id_fk')
                    ->references('id')
                    ->on('research_requests')
                    ->cascadeOnDelete();
            });
        });
    }
};
