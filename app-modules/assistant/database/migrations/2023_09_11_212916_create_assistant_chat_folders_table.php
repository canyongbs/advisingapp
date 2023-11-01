<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assistant_chat_folders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['name', 'user_id']);
        });
    }
};
