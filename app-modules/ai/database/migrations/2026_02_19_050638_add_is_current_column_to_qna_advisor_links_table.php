<?php

use App\Features\CurrentQnaAdvisorLinks;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('qna_advisor_links', function (Blueprint $table) {
                $table->boolean('is_current')->default(false);
            });

            CurrentQnaAdvisorLinks::activate();
        });
        
    }

    public function down(): void
    {
        DB::transaction(function () {
            CurrentQnaAdvisorLinks::deactivate();
            
            Schema::table('qna_advisor_links', function (Blueprint $table) {
                $table->dropColumn('is_current');
            });
        });
        
    }
};
