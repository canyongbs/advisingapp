<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('label');
            $table->text('type');
            $table->boolean('is_required');
            $table->json('config');

            $table->foreignUuid('form_id')->constrained('forms')->cascadeOnDelete();
            $table->foreignUuid('step_id')->nullable()->constrained('form_steps')->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
