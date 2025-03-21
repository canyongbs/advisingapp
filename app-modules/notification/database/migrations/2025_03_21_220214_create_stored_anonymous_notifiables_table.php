<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('stored_anonymous_notifiables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('route');
            $table->timestamps();

            $table->unique(['type', 'route']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_anonymous_notifiables');
    }
};
