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
        Schema::create('form_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('label');
            $table->string('key')->unique();
            $table->text('type');
            $table->json('content');
            $table->unsignedInteger('order');

            $table->foreignUuid('form_id')->constrained('forms');

            $table->timestamps();
        });
    }
};
