<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);

            $table->unique(['is_default', 'deleted_at']);
        });

        Schema::table('interaction_relations', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);

            $table->unique(['is_default', 'deleted_at']);
        });

        Schema::table('interaction_statuses', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);

            $table->unique(['is_default', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('interaction_relations', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('interaction_statuses', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
