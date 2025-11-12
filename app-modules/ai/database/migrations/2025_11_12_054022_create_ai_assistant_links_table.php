<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistant_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ai_assistant_id')->constrained('ai_assistants')->cascadeOnDelete();
            $table->string('url');
            $table->longText('parsing_results')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_links');
    }
};
