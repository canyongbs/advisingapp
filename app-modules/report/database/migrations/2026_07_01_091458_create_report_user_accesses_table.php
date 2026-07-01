<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_user_accesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_key');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['report_key', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_user_accesses');
    }
};
