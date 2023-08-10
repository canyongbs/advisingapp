<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToKnowledgeBaseItemsTable extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base_items', function (Blueprint $table) {
            $table->foreign('quality_id')->references('id')->on('knowledge_base_qualities');
            $table->foreign('status_id')->references('id')->on('knowledge_base_statuses');
            $table->foreign('category_id')->references('id')->on('knowledge_base_categories');
        });
    }
}
