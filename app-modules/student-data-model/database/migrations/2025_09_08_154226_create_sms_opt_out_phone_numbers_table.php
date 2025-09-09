<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sms_opt_out_phone_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->timestamps();

            $table->index('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_opt_out_phone_numbers');
    }
};
