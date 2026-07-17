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
