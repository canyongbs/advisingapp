<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('division_knowledge_base_item', function (Blueprint $table) {
            $table->foreignUuid('knowledge_base_item_id')->references('id')->on('knowledge_base_items');
            $table->foreignUuid('division_id')->references('id')->on('divisions');
        });
    }
};
