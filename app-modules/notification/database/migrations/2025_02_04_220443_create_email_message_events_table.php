<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('email_message_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_message_id')->constrained('email_messages')->cascadeOnDelete();
            $table->string('type');
            $table->jsonb('payload');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_message_events');
    }
};
