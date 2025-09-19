<?php

use App\Features\QnaAdvisorThreadInteractionRelationshipFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('qna_advisor_threads', function (Blueprint $table) {
                $table->foreignUuid('interaction_id')->nullable()->constrained();
            });

            QnaAdvisorThreadInteractionRelationshipFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            QnaAdvisorThreadInteractionRelationshipFeature::deactivate();

            Schema::table('qna_advisor_threads', function (Blueprint $table) {
                $table->dropColumn('interaction_id');
            });
        });
    }
};
