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
            $table->uuid('engagement_file_id');
            $table->string('entity_id');
            $table->string('entity_type');
            $table->timestamps();
        });
    }
};
