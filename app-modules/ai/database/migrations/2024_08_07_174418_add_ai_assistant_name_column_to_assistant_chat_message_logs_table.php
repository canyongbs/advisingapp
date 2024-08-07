<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assistant_chat_message_logs', function (Blueprint $table) {
            $table->string('ai_assistant_name');
        });
    }

    public function down(): void
    {
        Schema::table('assistant_chat_message_logs', function (Blueprint $table) {
            $table->dropColumn('ai_assistant_name');
        });
    }
};
