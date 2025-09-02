<?php

use App\Features\QnaAdvisorRequireAuthenticationFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('qna_advisors', function (Blueprint $table) {
                $table->boolean('is_requires_authentication_enabled')->default(false);
            });

            QnaAdvisorRequireAuthenticationFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            QnaAdvisorRequireAuthenticationFeature::deactivate();

            Schema::table('qna_advisors', function (Blueprint $table) {
                $table->dropColumn('is_requires_authentication_enabled');
            });
        });
    }
};
