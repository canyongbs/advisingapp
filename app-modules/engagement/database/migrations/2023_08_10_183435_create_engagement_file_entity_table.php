<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('engagement_file_entities', function (Blueprint $table) {
            $table->foreignUuid('engagement_file_id')->constrained('engagement_files')->cascadeOnDelete();
            $table->string('entity_id');
            $table->string('entity_type');
            $table->timestamps();

            $table->index(['entity_id', 'entity_type']);
        });
    }
};
