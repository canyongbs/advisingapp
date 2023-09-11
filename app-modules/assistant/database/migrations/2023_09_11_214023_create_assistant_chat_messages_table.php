<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assistant_chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assistant_chat_id')->constrained('assistant_chats');
            $table->longText('message');
            $table->enum('from', ['user', 'assistant']);
            $table->timestamps();
        });
    }
};
