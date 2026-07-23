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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends SettingsMigration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'case.view-any' => 'Case',
        'case.create' => 'Case',
        'case.*.view' => 'Case',
        'case.*.update' => 'Case',
        'case.*.delete' => 'Case',
        'case.*.restore' => 'Case',
        'case.*.force-delete' => 'Case',
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
            collect($this->guards)
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            DB::table('permission_groups')->where('name', 'Case')->delete();

            $this->migrator->deleteIfExists('portal.resource_hub_portal_case_management');

            DB::table('report_team_accesses')->where('report_key', 'student-case-report')->delete();
            DB::table('report_team_accesses')->where('report_key', 'prospect-case-report')->delete();
            DB::table('report_user_accesses')->where('report_key', 'student-case-report')->delete();
            DB::table('report_user_accesses')->where('report_key', 'prospect-case-report')->delete();

            DB::table('audits')->where('auditable_type', 'workflow_case_details')->delete();
            DB::table('audits')->where('auditable_type', 'case_assignments')->delete();
            DB::table('audits')->where('auditable_type', 'case_feedback')->delete();
            DB::table('audits')->where('auditable_type', 'cases')->delete();
            DB::table('audits')->where('auditable_type', 'case_priorities')->delete();
            DB::table('audits')->where('auditable_type', 'case_statuses')->delete();
            DB::table('audits')->where('auditable_type', 'case_types')->delete();
            DB::table('audits')->where('auditable_type', 'case_type_email_templates')->delete();
            DB::table('audits')->where('auditable_type', 'case_updates')->delete();
            DB::table('audits')->where('auditable_type', 'slas')->delete();

            Schema::dropIfExists('workflow_case_details');
            Schema::dropIfExists('case_type_email_templates');
            Schema::dropIfExists('case_type_auditors');
            Schema::dropIfExists('case_type_managers');
            Schema::dropIfExists('case_feedback');
            Schema::dropIfExists('case_histories');
            Schema::dropIfExists('case_assignments');
            Schema::dropIfExists('case_updates');
            Schema::dropIfExists('cases');
            Schema::dropIfExists('case_statuses');
            Schema::dropIfExists('case_form_field_submission');
            Schema::dropIfExists('case_form_submissions');
            Schema::dropIfExists('case_priorities');
            Schema::dropIfExists('case_form_authentications');
            Schema::dropIfExists('slas');
            Schema::dropIfExists('case_form_fields');
            Schema::dropIfExists('case_form_steps');
            Schema::dropIfExists('case_forms');
            Schema::dropIfExists('case_types');
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

            DB::table('permission_groups')->insert(['name' => 'Case']);

            $this->migrator->add('portal.resource_hub_portal_case_management', false);

            Schema::create('case_types', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');
                $table->boolean('has_enabled_feedback_collection')->default(false);
                $table->boolean('has_enabled_csat')->default(false);
                $table->boolean('has_enabled_nps')->default(false);
                $table->boolean('is_managers_case_created_email_enabled')->default(false);
                $table->boolean('is_managers_case_created_notification_enabled')->default(false);
                $table->boolean('is_managers_case_assigned_email_enabled')->default(false);
                $table->boolean('is_managers_case_assigned_notification_enabled')->default(false);
                $table->boolean('is_managers_case_closed_email_enabled')->default(false);
                $table->boolean('is_managers_case_closed_notification_enabled')->default(false);
                $table->boolean('is_auditors_case_created_email_enabled')->default(false);
                $table->boolean('is_auditors_case_created_notification_enabled')->default(false);
                $table->boolean('is_auditors_case_assigned_email_enabled')->default(false);
                $table->boolean('is_auditors_case_assigned_notification_enabled')->default(false);
                $table->boolean('is_auditors_case_closed_email_enabled')->default(false);
                $table->boolean('is_auditors_case_closed_notification_enabled')->default(false);
                $table->boolean('is_managers_case_update_email_enabled')->default(false);
                $table->boolean('is_managers_case_update_notification_enabled')->default(false);
                $table->boolean('is_managers_case_status_change_email_enabled')->default(false);
                $table->boolean('is_managers_case_status_change_notification_enabled')->default(false);
                $table->boolean('is_auditors_case_update_email_enabled')->default(false);
                $table->boolean('is_auditors_case_update_notification_enabled')->default(false);
                $table->boolean('is_auditors_case_status_change_email_enabled')->default(false);
                $table->boolean('is_auditors_case_status_change_notification_enabled')->default(false);
                $table->boolean('is_customers_case_created_email_enabled')->default(false);
                $table->boolean('is_customers_case_created_notification_enabled')->default(false);
                $table->boolean('is_customers_case_assigned_email_enabled')->default(false);
                $table->boolean('is_customers_case_assigned_notification_enabled')->default(false);
                $table->boolean('is_customers_case_update_email_enabled')->default(false);
                $table->boolean('is_customers_case_update_notification_enabled')->default(false);
                $table->boolean('is_customers_case_status_change_email_enabled')->default(false);
                $table->boolean('is_customers_case_status_change_notification_enabled')->default(false);
                $table->boolean('is_customers_case_closed_email_enabled')->default(false);
                $table->boolean('is_customers_case_closed_notification_enabled')->default(false);
                $table->boolean('is_customers_survey_response_email_enabled')->default(false);
                $table->string('assignment_type')->default('none');
                $table->foreignUuid('assignment_type_individual_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('last_assigned_id')->nullable()->constrained('users');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_forms', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('case_type_id')->nullable()->constrained('case_types');
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('embed_enabled')->default(false);
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('allowed_domains')->nullable();
                $table->string('primary_color')->nullable();
                $table->string('rounding')->nullable();
                $table->boolean('is_authenticated')->default(true);
                $table->boolean('is_wizard')->default(false);
                $table->boolean('recaptcha_enabled')->default(false);
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('content')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_form_steps', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->text('label');
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('content')->nullable();
                $table->foreignUuid('case_form_id')->constrained('case_forms')->cascadeOnDelete();
                $table->integer('sort');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_form_fields', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->text('label');
                $table->text('type');
                $table->boolean('is_required');
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('config');

                $table->foreignUuid('case_form_id')->constrained('case_forms')->cascadeOnDelete();
                $table->foreignUuid('case_form_step_id')->nullable()->constrained('case_form_steps')->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('slas', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedInteger('response_seconds')->nullable();
                $table->unsignedInteger('resolution_seconds')->nullable();
                $table->text('terms')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_form_authentications', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('author_id')->nullable();
                $table->string('author_type')->nullable();
                $table->string('code')->nullable();
                $table->foreignUuid('case_form_id')->constrained('case_forms')->cascadeOnDelete();

                $table->timestamps();

                $table->index(['author_type', 'author_id']);
            });

            Schema::create('case_priorities', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');
                $table->integer('order');
                $table->foreignUuid('sla_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignUuid('type_id')->constrained('case_types')->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_form_submissions', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('case_form_id')->constrained('case_forms')->cascadeOnDelete();
                $table->foreignUuid('case_priority_id')->nullable()->constrained('case_priorities');
                $table->string('author_id')->nullable();
                $table->string('author_type')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->string('request_method')->nullable();
                $table->text('request_note')->nullable();
                $table->foreignUuid('requester_id')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_type', 'author_id']);
            });

            Schema::create('case_form_field_submission', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->longText('response');
                $table->foreignUuid('case_form_field_id')->constrained('case_form_fields')->cascadeOnDelete();
                $table->foreignUuid('case_form_submission_id')->constrained('case_form_submissions')->cascadeOnDelete();

                $table->timestamps();
            });

            Schema::create('case_statuses', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('classification');
                $table->string('name');
                $table->string('color');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('cases', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('case_number')->unique();
                $table->string('respondent_type');
                $table->string('respondent_id');
                $table->longText('close_details')->nullable();
                $table->longText('res_details')->nullable();

                $table->foreignUuid('case_form_submission_id')->nullable()->constrained('case_form_submissions');
                $table->foreignUuid('division_id')->constrained('divisions');
                $table->foreignUuid('status_id')->nullable()->constrained('case_statuses');
                $table->foreignUuid('priority_id')->nullable()->constrained('case_priorities');
                $table->foreignUuid('created_by_id')->nullable()->constrained('users');
                $table->timestamp('status_updated_at')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_updates', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('case_model_id')->nullable()->constrained('cases');
                $table->text('update');
                $table->boolean('internal');
                $table->string('direction');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_assignments', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('case_model_id')->constrained('cases');
                $table->foreignUuid('user_id')->constrained('users');
                $table->foreignUuid('assigned_by_id')->nullable()->constrained('users');
                $table->timestamp('assigned_at');
                $table->string('status')->default('active');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_histories', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('case_model_id')->constrained('cases');
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('original_values');
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('new_values');

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_feedback', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('case_id')->constrained()->cascadeOnDelete();
                $table->string('assignee_type');
                $table->string('assignee_id');
                $table->unsignedInteger('csat_answer')->nullable();
                $table->unsignedInteger('nps_answer')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('case_type_managers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('case_type_id')->constrained('case_types')->cascadeOnDelete();
                $table->foreignUuid('team_id')->constrained('teams')->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('case_type_auditors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('case_type_id')->constrained('case_types')->cascadeOnDelete();
                $table->foreignUuid('team_id')->constrained('teams')->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('case_type_email_templates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('case_type_id')->constrained('case_types');
                $table->string('type');
                $table->jsonb('subject');
                $table->jsonb('body');
                $table->string('role')->nullable();
                $table->timestamps();

                $table->unique(['case_type_id', 'type', 'role']);
            });

            Schema::create('workflow_case_details', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('division_id')->constrained('divisions');
                $table->foreignUuid('status_id')->constrained('case_statuses');
                $table->foreignUuid('priority_id')->constrained('case_priorities');
                $table->foreignUuid('assigned_to_id')->nullable()->constrained('users');
                $table->longText('close_details')->nullable();
                $table->longText('res_details')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        });
    }
};
