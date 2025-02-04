<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sms_message_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sms_message_id')->constrained('sms_messages')->cascadeOnDelete();
            $table->string('type');
            $table->jsonb('payload');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_message_events');
    }
};
