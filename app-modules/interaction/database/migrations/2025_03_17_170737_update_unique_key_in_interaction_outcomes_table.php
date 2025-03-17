<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->dropUnique(['is_default', 'deleted_at']);
            DB::statement('
            CREATE UNIQUE INDEX interaction_outcomes_is_default_unique 
            ON interaction_outcomes (is_default) 
            WHERE is_default = true AND deleted_at IS NULL;
        ');
        });
    }

    public function down(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            DB::statement('DROP INDEX IF EXISTS interaction_outcomes_is_default_unique;');
            $table->unique(['is_default', 'deleted_at']);
        });
    }
};
