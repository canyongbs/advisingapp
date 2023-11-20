<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assistant_chats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assistant_chat_folder_id')->nullable()->constrained('assistant_chat_folders')->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
