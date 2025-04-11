<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->boolean('sms_opt_out')->nullable()->change();
            $table->boolean('email_bounce')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->boolean('sms_opt_out')->default(false)->change();
            $table->boolean('email_bounce')->default(false)->change();
        });
    }
};
