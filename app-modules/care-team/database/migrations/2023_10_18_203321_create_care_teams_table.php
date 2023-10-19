<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('care_teams', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('educatable_id');
            $table->string('educatable_type');

            $table->foreignUuid('user_id')->constrained('users');

            $table->unique(['educatable_id', 'educatable_type', 'user_id']);

            $table->timestamps();
        });
    }
};
