<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('form_id')->constrained('forms')->cascadeOnDelete();
            $table->string('author_id')->nullable();
            $table->string('author_type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['author_type', 'author_id']);
        });
    }
};
