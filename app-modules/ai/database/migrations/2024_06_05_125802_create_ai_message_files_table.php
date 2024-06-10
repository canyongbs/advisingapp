<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_message_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('message_id')->constrained('ai_messages')->cascadeOnDelete();
            $table->string('file_id')->nullable();
            $table->string('name')->nullable();
            $table->text('temporary_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_message_files');
    }
};
