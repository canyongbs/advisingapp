<?php

use App\Features\QnaAdvisorGenerateProspectsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('qna_advisors', function (Blueprint $table) {
                $table->boolean('is_generate_prospects_enabled')->default(false);
            });

            QnaAdvisorGenerateProspectsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            QnaAdvisorGenerateProspectsFeature::deactivate();

            Schema::table('qna_advisors', function (Blueprint $table) {
                $table->dropColumn('is_generate_prospects_enabled');
            });
        });
    }
};
