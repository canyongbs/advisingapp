<?php

use App\Features\WorkingHousFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('working_hours');
            });

            WorkingHousFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            WorkingHousFeature::deactivate();

            Schema::table('users', function (Blueprint $table) {
                $table->jsonb('working_hours')->nullable();
            });
        });
    }
};
