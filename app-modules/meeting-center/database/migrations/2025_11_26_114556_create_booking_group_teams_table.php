<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('booking_group_teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_group_id')->constrained('booking_groups')->cascadeOnDelete();
            $table->foreignUuid('team_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_group_teams');
    }
};
