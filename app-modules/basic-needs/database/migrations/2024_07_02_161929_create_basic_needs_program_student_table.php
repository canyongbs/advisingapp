<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('basic_needs_program_student', function (Blueprint $table) {
            $table->foreignUuid('basic_needs_program_id')->constrained('basic_needs_programs')->cascadeOnDelete();
            $table->string('student_id')->constrained('students', 'sisid')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basic_needs_program_student');
    }
};
