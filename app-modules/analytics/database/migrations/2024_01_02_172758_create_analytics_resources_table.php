<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('analytics_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_included_in_data_portal');

            $table->foreignUuid('source_id')->nullable()->constrained('analytics_resource_sources');
            $table->foreignUuid('category_id')->constrained('analytics_resource_categories');

            $table->timestamps();
        });
    }
};
