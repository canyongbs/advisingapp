<?php

use App\Features\LockAiThreadsAfterAssistantUpdateFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('ai_threads', function (Blueprint $table) {
                $table->string('locked_reason')->nullable();
            });

            LockAiThreadsAfterAssistantUpdateFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            LockAiThreadsAfterAssistantUpdateFeature::deactivate();

            Schema::table('ai_threads', function (Blueprint $table) {
                $table->dropColumn('locked_reason');
            });
        });
    }
};
