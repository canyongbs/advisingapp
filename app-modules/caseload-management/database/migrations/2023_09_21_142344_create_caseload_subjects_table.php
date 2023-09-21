<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('caseload_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject_id');
            $table->string('subject_type');
            $table->foreignUuid('caseload_id')->constrained('caseloads');

            $table->unique(['subject_id', 'subject_type', 'caseload_id']);

            $table->timestamps();
        });
    }
};
