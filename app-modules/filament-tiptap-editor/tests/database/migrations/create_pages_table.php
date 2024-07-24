<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->longText('html_content')->nullable();
            $table->longText('json_content')->nullable();
            $table->longText('text_content')->nullable();

            $table->timestamps();
        });
    }
};
