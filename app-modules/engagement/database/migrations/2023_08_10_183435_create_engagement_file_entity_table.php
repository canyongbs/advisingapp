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
        Schema::create('engagement_file_entity', function (Blueprint $table) {
            $table->integer('engagement_file_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->string('entity_type');
            $table->timestamps();
        });
    }
};
