<?php

use App\Features\StudentSisCategoryFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('students', function (Blueprint $table) {
                $table->string('sis_category')->nullable();
            });

            StudentSisCategoryFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('sis_category');
            });

            StudentSisCategoryFeature::deactivate();
        });
    }
};
