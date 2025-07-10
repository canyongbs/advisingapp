<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('research_request_parsed_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
            $table->longText('results');
            $table->text('url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_request_parsed_links');
    }
};
