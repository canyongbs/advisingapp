<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_field_submission', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->longText('response');
            $table->foreignUuid('field_id')->constrained('form_fields')->cascadeOnDelete();
            $table->foreignUuid('submission_id')->constrained('form_submissions')->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
