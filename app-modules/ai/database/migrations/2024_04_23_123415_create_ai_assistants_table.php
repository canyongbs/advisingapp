<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('assistant_id')->nullable();
            $table->string('name');
            $table->string('application');
            $table->string('model');
            $table->boolean('is_default')->default(false);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->longText('knowledge')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistants');
    }
};
