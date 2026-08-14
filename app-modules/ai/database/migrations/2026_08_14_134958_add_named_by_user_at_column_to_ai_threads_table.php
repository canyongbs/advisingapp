<?php

use App\Features\AiThreadAutoNamingFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('ai_threads', function (Blueprint $table) {
                $table->dateTime('named_by_user_at')->nullable();
            });

            AiThreadAutoNamingFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AiThreadAutoNamingFeature::deactivate();

            Schema::table('ai_threads', function (Blueprint $table) {
                $table->dropColumn('named_by_user_at');
            });
        });
    }
};
