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

use App\Features\EventVersioningFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('event_registration_forms', function (Blueprint $table) {
                $table->uuid('root_id')->nullable();
                $table->timestamp('archived_at')->nullable();
            });
            // TODO: Cleanup Task - EventVersioningFeature - This backfill can be removed once all environments have run this migration
            DB::update('UPDATE event_registration_forms SET root_id = id WHERE root_id IS NULL');

            Schema::table('event_registration_forms', function (Blueprint $table) {
                $table->uuid('root_id')->nullable(false)->change();
                $table->foreign('root_id')->references('id')->on('event_registration_forms');
                $table->index('root_id');
            });

            // Archive all but the most recently created active form per event
            // so the partial unique index can be created cleanly on existing data.
            DB::statement(
                'UPDATE event_registration_forms
                 SET archived_at = NOW()
                 WHERE archived_at IS NULL
                   AND id NOT IN (
                       SELECT DISTINCT ON (event_id) id
                       FROM event_registration_forms
                       WHERE archived_at IS NULL
                       ORDER BY event_id, created_at DESC
                   )'
            );

            Schema::table('event_registration_forms', function (Blueprint $table) {
                $table->uniqueIndex(['event_id'])->where(fn (Builder $condition) => $condition->whereNull('archived_at'));
            });

            EventVersioningFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            EventVersioningFeature::deactivate();

            Schema::table('event_registration_forms', function (Blueprint $table) {
                $table->dropIndex('event_registration_forms_event_id_unique');
            });

            Schema::table('event_registration_forms', function (Blueprint $table) {
                $table->dropForeign(['root_id']);
                $table->dropIndex(['root_id']);
                $table->dropColumn(['root_id', 'archived_at']);
            });
        });
    }
};
