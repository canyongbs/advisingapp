<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('twilio_conversations', function (Blueprint $table) {
            $table->string('sid')->primary();
            $table->string('friendly_name')->nullable();
            $table->string('type');
            $table->timestamps();
        });
    }
};
