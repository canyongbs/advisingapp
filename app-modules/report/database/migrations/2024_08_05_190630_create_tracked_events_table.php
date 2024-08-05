<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tracked_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->timestamp('occurred_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_events');
    }
};
