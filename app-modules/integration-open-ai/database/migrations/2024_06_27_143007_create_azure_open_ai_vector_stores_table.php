<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('azure_open_ai_vector_stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vector_store_id');
            $table->foreignUuid('vector_storable_id');
            $table->string('vector_storable_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_open_ai_vector_stores');
    }
};
