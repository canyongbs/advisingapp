<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('classification');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_statuses');
    }
};
