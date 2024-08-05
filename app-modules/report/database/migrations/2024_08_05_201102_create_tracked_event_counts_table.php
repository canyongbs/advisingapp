<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tracked_event_counts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->unique();
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamp('last_occurred_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_event_counts');
    }
};
