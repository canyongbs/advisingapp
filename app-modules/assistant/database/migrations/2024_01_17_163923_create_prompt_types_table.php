<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('prompt_types', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title')->unique();
            $table->longText('description')->nullable();

            $table->timestamps();
        });
    }
};
