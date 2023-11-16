<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('twilio_conversation_user', function (Blueprint $table) {
            $table->string('conversation_sid');
            $table->foreignUuid('user_id');
            $table->string('participant_sid');
            $table->timestamps();

            $table->foreign('conversation_sid')->references('sid')->on('twilio_conversations')->cascadeOnDelete();
        });
    }
};
