<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_teams', function (Blueprint $table) {
            $table->foreignUuid('care_team_role_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('care_teams', function (Blueprint $table) {
            $table->dropForeign('care_team_role_id');
            $table->dropColumn('care_team_role_id');
        });
    }
};
