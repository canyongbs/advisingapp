<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('low_earned_credit_percentage_alert_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('minimum_earned_credit_percentage')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('low_earned_credit_percentage_alert_configurations');
    }
};
