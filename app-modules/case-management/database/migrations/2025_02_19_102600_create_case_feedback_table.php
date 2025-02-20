<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('case_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained()->cascadeOnDelete();
            $table->string('assignee_type');
            $table->string('assignee_id');
            $table->unsignedInteger('csat_answer')->nullable();
            $table->unsignedInteger('nps_answer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_feedback');
    }
};
