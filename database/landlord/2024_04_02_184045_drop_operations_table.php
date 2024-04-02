<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('operations');
    }

    public function down(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('dispatched');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }
};
