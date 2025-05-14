<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('campaign_actions', function (Blueprint $table) {
                $table->rename('last_execution_attempt_at', 'execution_dispatched_at');
                $table->timestampTz('execution_dispatched_at')->nullable()->change();

                $table->rename('successfully_executed_at', 'execution_finished_at');
                $table->timestampTz('execution_finished_at')->nullable()->change();

                $table->dropColumn('last_execution_attempt_error');
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('campaign_actions', function (Blueprint $table) {
                $table->rename('execution_dispatched_at', 'last_execution_attempt_at');
                $table->timestamp('last_execution_attempt_at')->nullable()->change();

                $table->rename('execution_finished_at', 'successfully_executed_at');
                $table->timestamp('successfully_executed_at')->nullable()->change();

                $table->string('last_execution_attempt_error')->nullable();
            });
        });
    }
};
