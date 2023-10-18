<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('type');
            $table->text('provider_id');
            $table->text('oauth_token');
            $table->text('oauth_refresh_token');

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamp('oauth_token_expires_at');

            $table->timestamps();
        });
    }
};
