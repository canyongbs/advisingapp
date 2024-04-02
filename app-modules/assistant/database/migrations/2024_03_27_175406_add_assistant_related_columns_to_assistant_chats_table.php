<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->string('assistant_id')->nullable();
            $table->string('thread_id')->nullable();
            $table->string('run_id')->nullable();
        });

        Schema::table('assistant_chat_messages', function (Blueprint $table) {
            $table->string('message_id')->nullable();
        });
    }
};
