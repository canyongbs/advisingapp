<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagement_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->date('retention_date')->nullable();
            $table->timestamps();
        });
    }
};
