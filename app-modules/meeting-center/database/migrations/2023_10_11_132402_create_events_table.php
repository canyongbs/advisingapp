<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('provider_type')->nullable();
            $table->string('provider_id')->nullable();

            $table->foreignUuid('user_id')->constrained('users');

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->timestamps();
        });
    }
};
