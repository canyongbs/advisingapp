<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropForeign(['division_id']);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_division_visible_on_profile');
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreignUuid('division_id')->nullable()->constrained('divisions');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_division_visible_on_profile')->default(false);
            });
        });
    }
};
