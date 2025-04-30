<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropForeignUuid('team_id');
            $table->dropForeignUuid('user_id');
        });

        Schema::dropIfExists('team_user');
    }

    public function down(): void
    {
        Schema::create('team_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')
                ->constrained('teams');
            $table->foreignUuid('user_id')
                ->constrained('users');
            $table->timestamps();
        });
    }
};
