<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('assistant_chat_message_logs', function (Blueprint $table) {
            $table->string('feature')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assistant_chat_message_logs', function (Blueprint $table) {
            $table->string('feature')->nullable(false)->change();
        });
    }
};
