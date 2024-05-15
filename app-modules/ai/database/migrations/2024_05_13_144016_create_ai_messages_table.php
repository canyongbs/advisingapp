<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('message_id')->nullable();
            $table->text('content');
            $table->foreignUuid('thread_id')->constrained('ai_threads')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_thread_messages');
    }
};
