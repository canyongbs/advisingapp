<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_email_auto_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject')->nullable();
            $table->json('body')->nullable();
            $table->boolean('is_enabled')->default(false);

            $table->foreignUuid('form_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
