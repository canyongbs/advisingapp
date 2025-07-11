<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('open_ai_research_request_vector_stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('research_request_id')->constrained();
            $table->text('deployment_hash');
            $table->dateTime('ready_until')->nullable();
            $table->string('vector_store_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_ai_research_request_vector_stores');
    }
};
