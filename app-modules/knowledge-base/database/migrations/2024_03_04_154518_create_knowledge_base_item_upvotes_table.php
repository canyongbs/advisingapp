<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('knowledge_base_item_upvotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('knowledge_base_item_id')->constrained('knowledge_base_articles')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
