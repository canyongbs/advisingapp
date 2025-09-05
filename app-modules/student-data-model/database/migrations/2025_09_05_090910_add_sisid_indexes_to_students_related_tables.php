<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index('sisid');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->index('sisid');
        });

        Schema::table('student_email_addresses', function (Blueprint $table) {
            $table->index('sisid');
        });

        Schema::table('student_phone_numbers', function (Blueprint $table) {
            $table->index('sisid');
        });

        Schema::table('student_addresses', function (Blueprint $table) {
            $table->index('sisid');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['sisid']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['sisid']);
        });

        Schema::table('student_email_addresses', function (Blueprint $table) {
            $table->dropIndex(['sisid']);
        });

        Schema::table('student_phone_numbers', function (Blueprint $table) {
            $table->dropIndex(['sisid']);
        });

        Schema::table('student_addresses', function (Blueprint $table) {
            $table->dropIndex(['sisid']);
        });
    }
};
