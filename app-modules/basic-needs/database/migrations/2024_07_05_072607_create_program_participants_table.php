<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('program_participants', function (Blueprint $table) {
            $table->foreignUuid('basic_needs_program_id')->constrained('basic_needs_programs')->cascadeOnDelete();
            $table->string('program_participants_type');
            $table->string('program_participants_id');
            $table->timestamps();

            $table->index(['program_participants_type', 'program_participants_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_participants');
    }
};
