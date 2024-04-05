<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('section')->nullable();
            $table->string('name')->nullable();
            $table->string('department')->nullable();
            $table->string('faculty_name')->nullable();
            $table->string('faculty_email')->nullable();
            $table->string('semester_code')->nullable();
            $table->string('semester_name')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('section');
            $table->dropColumn('name');
            $table->dropColumn('department');
            $table->dropColumn('faculty_name');
            $table->dropColumn('faculty_email');
            $table->dropColumn('semester_code');
            $table->dropColumn('semester_name');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
};
