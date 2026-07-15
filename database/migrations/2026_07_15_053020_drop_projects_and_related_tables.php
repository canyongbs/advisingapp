<?php

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
            Schema::dropIfExists('project_milestone_statuses');
            Schema::dropIfExists('project_milestones');
            Schema::dropIfExists('project_manager_users');
            Schema::dropIfExists('project_manager_teams');
            Schema::dropIfExists('project_auditor_users');
            Schema::dropIfExists('project_auditor_teams');
            Schema::dropIfExists('confidential_task_projects');
            Schema::dropIfExists('projects');
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
