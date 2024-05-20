<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('thread_id')->nullable();
            $table->string('name')->nullable();
            $table->foreignUuid('assistant_id')->constrained('ai_assistants')->cascadeOnDelete();
            $table->foreignUuid('folder_id')->nullable()->constrained('ai_thread_folders')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('ai_threads')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_threads');
    }
};
