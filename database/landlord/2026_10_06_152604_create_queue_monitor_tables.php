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
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

/**
 * The tables of cboxdk/laravel-queue-monitor (its `create_queue_monitor_jobs_table` and `extend_autoscale_v3_support`
 * migrations), owned here so they live in the landlord database. Statuses and worker types are strings rather than
 * enums so a package upgrade adding one cannot be rejected by a check constraint.
 */
return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('queue_monitor_jobs', function (Blueprint $table) {
                $table->id();

                $table->string('uuid', 36)->index();
                $table->string('job_id')->nullable()->index();
                $table->string('job_class')->index();
                $table->string('display_name')->nullable();

                $table->string('connection')->index();
                $table->string('queue')->index();

                $table->uuid('tenant_id')->nullable()->index();

                $table->longText('payload')->nullable();

                $table->string('status')->index();
                $table->unsignedInteger('attempt')->default(1);
                $table->unsignedInteger('max_attempts')->default(1);

                $table->unsignedBigInteger('retried_from_id')->nullable();
                $table->foreign('retried_from_id')
                    ->references('id')
                    ->on('queue_monitor_jobs')
                    ->nullOnDelete();

                $table->string('server_name')->index();
                $table->string('worker_id')->index();
                $table->string('worker_type');

                $table->decimal('cpu_time_ms', 10, 2)->nullable();
                $table->decimal('memory_peak_mb', 10, 2)->nullable();
                $table->decimal('worker_memory_limit_mb', 10, 2)->nullable();
                $table->unsignedInteger('file_descriptors')->nullable();

                $table->unsignedInteger('duration_ms')->nullable()->index();

                $table->string('exception_class')->nullable()->index();
                $table->text('exception_message')->nullable();
                $table->longText('exception_trace')->nullable();

                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('tags')->nullable();

                $table->timestamp('queued_at')->index();
                $table->timestamp('available_at')->nullable();
                $table->timestamp('started_at')->nullable()->index();
                $table->timestamp('completed_at')->nullable()->index();
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index(['queue', 'status', 'created_at']);
                $table->index(['job_class', 'status']);
                $table->index(['server_name', 'worker_id', 'status']);
                $table->index(['status', 'completed_at']);
                $table->index(['queue', 'created_at']);
                $table->index(['job_class', 'completed_at', 'duration_ms']);
                $table->index(['status', 'started_at']);
                $table->index(['worker_type', 'created_at']);
                $table->index('created_at');

                // Pruning deletes rows, and the self-referencing foreign key otherwise scans the table for each one.
                $table->index('retried_from_id')->where('retried_from_id is not null');
            });

            Schema::create('queue_monitor_tags', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->string('tag')->index();

                $table->foreign('job_id')
                    ->references('id')
                    ->on('queue_monitor_jobs')
                    ->cascadeOnDelete();

                $table->unique(['job_id', 'tag']);
                $table->timestamps();
            });

            Schema::create('queue_monitor_scaling_events', function (Blueprint $table) {
                $table->id();
                $table->string('connection');
                $table->string('queue');
                $table->string('action');
                $table->integer('current_workers');
                $table->integer('target_workers');
                $table->string('reason');
                $table->decimal('predicted_pickup_time', 10, 2)->nullable();
                $table->integer('sla_target')->default(30);
                $table->boolean('sla_breach_risk')->default(false);
                $table->integer('breach_seconds')->nullable();
                $table->decimal('breach_percentage', 8, 2)->nullable();
                $table->integer('margin_seconds')->nullable();
                $table->decimal('margin_percentage', 8, 2)->nullable();
                $table->integer('pending')->nullable();
                $table->integer('active_workers')->nullable();
                $table->timestamps();

                $table->index(['queue', 'created_at']);
                $table->index(['action', 'created_at']);
            });

            Schema::create('queue_monitor_cluster_events', function (Blueprint $table) {
                $table->id();
                $table->string('cluster_id');
                $table->string('manager_id')->nullable();
                $table->string('event_type');
                $table->string('host')->nullable();
                $table->string('leader_id')->nullable();
                $table->string('previous_leader_id')->nullable();
                $table->integer('current_hosts')->nullable();
                $table->integer('recommended_hosts')->nullable();
                $table->integer('current_capacity')->nullable();
                $table->integer('required_workers')->nullable();
                $table->string('action')->nullable();
                $table->text('reason')->nullable();
                // @phpstan-ignore Common.jsonColumnInMigration
                $table->json('meta')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['cluster_id', 'event_type', 'created_at'], 'ce_cluster_type_time');
                $table->index(['event_type', 'created_at'], 'ce_type_time');
                $table->index(['manager_id', 'created_at'], 'ce_manager_time');
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::dropIfExists('queue_monitor_cluster_events');
            Schema::dropIfExists('queue_monitor_scaling_events');
            Schema::dropIfExists('queue_monitor_tags');
            Schema::dropIfExists('queue_monitor_jobs');
        });
    }
};
