<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('application_submissions_checklist_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_submission_id')->references('id')->on('application_submissions')->onDelete('cascade');
            $table->string('title');
            $table->boolean('is_checked')->default(false);
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignUuid('completed_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamp('completed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_submissions_checklist_items');
    }
};
