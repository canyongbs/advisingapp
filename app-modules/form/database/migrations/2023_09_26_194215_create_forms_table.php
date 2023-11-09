<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('embed_enabled')->default(false);
            $table->json('allowed_domains')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('rounding')->nullable();
            $table->boolean('is_wizard')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
