<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistant_resource_hub_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ai_assistant_id')->constrained('ai_assistants')->cascadeOnDelete();
            $table->foreignUuid('resource_hub_category_id')->constrained('resource_hub_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_resource_hub_categories');
    }
};
