<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pipeline_educatable', function (Blueprint $table) {
            $table->foreignUuid('pipeline_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('pipeline_stage_id')->constrained()->onDelete('cascade');
            $table->string('educatable_type');
            $table->string('educatable_id');
            $table->timestamps();

            $table->index(['educatable_type', 'educatable_id', 'pipeline_id', 'pipeline_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_educatable');
    }
};
