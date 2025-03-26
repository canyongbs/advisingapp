<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_team_roles_prospects_users', function (Blueprint $table) {
            $table->foreignId('care_team_role_id')->constrained('care_team_roles')->cascadeOnDelete();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['care_team_role_id','prospect_id','user_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_team_roles_prospects_users');
    }
};
