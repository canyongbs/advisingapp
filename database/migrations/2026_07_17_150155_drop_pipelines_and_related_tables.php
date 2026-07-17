<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $permissions
     */
    private array $permissions = [
        'pipeline.view-any' => 'Pipeline',
        'pipeline.create' => 'Pipeline',
        'pipeline.*.view' => 'Pipeline',
        'pipeline.*.update' => 'Pipeline',
        'pipeline.*.delete' => 'Pipeline',
        'pipeline.*.restore' => 'Pipeline',
        'pipeline.*.force-delete' => 'Pipeline',
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

            Schema::dropIfExists('educatable_pipeline_stages');
            Schema::dropIfExists('pipeline_stages');
            Schema::dropIfExists('pipelines');
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

            Schema::create('pipelines', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('description');
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignUuid('segment_id')->constrained('segments')->cascadeOnDelete();
                $table->string('default_stage');
                $table->unsignedBigInteger('order');
                $table->timestamps();
            });

            Schema::create('pipeline_stages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->foreignUuid('pipeline_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('order');
                $table->timestamps();
                $table->unique(['name', 'pipeline_id']);
            });

            Schema::create('educatable_pipeline_stages', function (Blueprint $table) {
                $table->foreignUuid('pipeline_id')->constrained()->onDelete('cascade');
                $table->foreignUuid('pipeline_stage_id')->constrained()->onDelete('cascade');
                $table->string('educatable_type');
                $table->string('educatable_id');
                $table->timestamps();
                $table->index(['educatable_type', 'educatable_id', 'pipeline_id', 'pipeline_stage_id']);
            });
        });
    }
};
