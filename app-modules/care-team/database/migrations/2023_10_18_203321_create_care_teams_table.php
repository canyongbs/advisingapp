<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('care_teams', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('educatable_id');
            $table->string('educatable_type');

            $table->unique(['educatable_id', 'educatable_type']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_teams');
    }
};
