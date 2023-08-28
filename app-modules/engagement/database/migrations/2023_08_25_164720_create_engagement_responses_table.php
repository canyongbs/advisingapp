<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagement_responses', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id')->nullable();
            $table->string('sender_type')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }
};
