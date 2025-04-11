<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('sms_opt_out')->nullable()->change();
            $table->boolean('email_bounce')->nullable()->change();
            $table->boolean('dual')->nullable()->change();
            $table->boolean('ferpa')->nullable()->change();
            $table->boolean('sap')->nullable()->change();
            $table->boolean('firstgen')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('sms_opt_out')->default(false)->change();
            $table->boolean('email_bounce')->default(false)->change();
            $table->boolean('dual')->default(false)->change();
            $table->boolean('ferpa')->default(false)->change();
            $table->boolean('sap')->default(false)->change();
            $table->boolean('firstgen')->default(false)->change();
        });
    }
};
