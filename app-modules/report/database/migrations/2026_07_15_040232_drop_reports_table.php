<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reports');
    }

    public function down(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();
            $table->jsonb('filters')->nullable();
            $table->jsonb('columns');
            $table->string('model');

            $table->foreignUuid('user_id')->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
