<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->string('timelineable_type');
            $table->foreignUuid('timelineable_id');
            $table->timestamp('record_sortable_date');
            $table->timestamps();
        });
    }
};
