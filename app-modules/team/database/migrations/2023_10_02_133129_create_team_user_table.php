<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('team_user', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('team_id')->constrained('teams');
            $table->foreignUuid('user_id')
                //TODO: remove this if we support multiple teams
                ->unique()
                ->constrained('users');

            $table->timestamps();
        });
    }
};
