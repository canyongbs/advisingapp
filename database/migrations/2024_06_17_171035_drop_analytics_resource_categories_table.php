<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('analytics_resource_categories');
    }

    public function down(): void
    {
        Schema::table('analytics_resource_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->string('classification');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
