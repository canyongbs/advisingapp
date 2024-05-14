<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('interaction_initiatives', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
        });

        Schema::table('interaction_drivers', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
        });

        Schema::table('interaction_types', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('interaction_initiatives', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('interaction_drivers', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('interaction_types', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
