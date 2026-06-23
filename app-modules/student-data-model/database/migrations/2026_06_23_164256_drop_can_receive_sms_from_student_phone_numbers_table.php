<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('student_phone_numbers', function (Blueprint $table) {
            $table->dropColumn('can_receive_sms');
        });
    }

    public function down(): void
    {
        Schema::table('student_phone_numbers', function (Blueprint $table) {
            $table->boolean('can_receive_sms')->default(false);
        });
    }
};
