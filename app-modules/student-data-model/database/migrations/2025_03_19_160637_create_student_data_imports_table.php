<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('student_data_imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('students_import_id')->constrained('imports')->cascadeOnDelete();
            $table->foreignUuid('email_addresses_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->foreignUuid('phone_numbers_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->foreignUuid('addresses_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->foreignUuid('programs_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->foreignUuid('enrollments_import_id')->nullable()->constrained('imports')->nullOnDelete();
            $table->string('job_batch_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_data_imports');
    }
};
