<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('application_authentications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->string('code')->nullable();
            $table->foreignUuid('application_id')->constrained('applications')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['author_type', 'author_id']);
        });
    }
};
