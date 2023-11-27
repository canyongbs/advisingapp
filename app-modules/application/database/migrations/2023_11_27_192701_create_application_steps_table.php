<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('application_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('label');
            $table->json('content')->nullable();
            $table->foreignUuid('application_id')->constrained()->on('applications')->cascadeOnDelete();
            $table->integer('sort');

            $table->timestamps();
        });
    }
};
