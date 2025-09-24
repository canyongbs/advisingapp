<?php

use App\Features\AlertVisibleToStudentsFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('alerts', function (Blueprint $table) {
                $table->boolean('is_visible_for_students');
            });

            AlertVisibleToStudentsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AlertVisibleToStudentsFeature::purge();

            Schema::table('alerts', function (Blueprint $table) {
                $table->dropColumn('is_visible_for_students');
            });
        });
    }
};
