<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKnowledgeBaseItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('question');
            $table->boolean('public');
            $table->longText('solution')->nullable();
            $table->longText('notes')->nullable();
            $table->uuid('quality_id')->nullable();
            $table->uuid('status_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
