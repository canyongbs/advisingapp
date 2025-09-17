<?php

use App\Features\EnrollmentSemesterFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('enrollment_semesters', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name')->index();
                $table->unsignedInteger('order');
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table('enrollments', function (Blueprint $table) {
                $table->index('semester_name');
            });

            EnrollmentSemesterFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            EnrollmentSemesterFeature::deactivate();

            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropIndex(['semester_name']);
            });

            Schema::dropIfExists('enrollment_semesters');
        });
    }
};
