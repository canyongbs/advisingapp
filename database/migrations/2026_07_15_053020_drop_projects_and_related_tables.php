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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'project.view-any' => 'Project',
        'project.create' => 'Project',
        'project.*.view' => 'Project',
        'project.*.update' => 'Project',
        'project.*.delete' => 'Project',
        'project.*.restore' => 'Project',
        'project.*.force-delete' => 'Project',
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

            Schema::table('pipelines', function (Blueprint $table) {
                $table->dropConstrainedForeignId('project_id');
            });

            Schema::table('tasks', function (Blueprint $table) {
                $table->dropConstrainedForeignId('project_id');
            });

            Schema::dropIfExists('project_files');
            Schema::dropIfExists('project_milestones');
            Schema::dropIfExists('project_milestone_statuses');
            Schema::dropIfExists('project_manager_users');
            Schema::dropIfExists('project_manager_teams');
            Schema::dropIfExists('project_auditor_users');
            Schema::dropIfExists('project_auditor_teams');
            Schema::dropIfExists('confidential_task_projects');
            Schema::dropIfExists('projects');

            DB::table('report_team_accesses')->where('report_key', 'project-report')->delete();
            DB::table('report_user_accesses')->where('report_key', 'project-report')->delete();

            DB::table('audits')->whereIn('auditable_type', [
                'project',
                'project_file',
                'project_milestone',
                'project_milestone_status',
            ])->delete();
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

            Schema::create('projects', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name')->unique();
                $table->longText('description')->nullable();
                $table->UuidMorphs('created_by');
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('project_files', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('project_id')->constrained('projects');
                $table->string('description');
                $table->date('retention_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('project_milestone_statuses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('project_milestones', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title')->unique();
                $table->string('description')->nullable();
                $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
                $table->foreignUuid('status_id')->constrained('project_milestone_statuses');
                $table->foreignUuid('created_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->date('target_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('project_manager_users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('project_manager_teams', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('project_auditor_users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('project_auditor_teams', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('confidential_task_projects', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();

                $table->timestamps();
            });

            Schema::table('pipelines', function (Blueprint $table) {
                $table->foreignUuid('project_id')->nullable()->constrained()->cascadeOnDelete();
            });

            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignUuid('project_id')->nullable()->constrained('projects');
            });
        });
    }
};
