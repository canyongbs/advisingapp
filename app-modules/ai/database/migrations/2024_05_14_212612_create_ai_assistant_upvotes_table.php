<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistant_upvotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assistant_id')->constrained('ai_assistants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_upvotes');
    }
};
