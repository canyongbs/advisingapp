<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->foreignUuid('interaction_initiative_id')->nullable()->constrained('interaction_initiatives');
        });
    }

    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropColumn('interaction_initiative_id');
        });
    }
};
