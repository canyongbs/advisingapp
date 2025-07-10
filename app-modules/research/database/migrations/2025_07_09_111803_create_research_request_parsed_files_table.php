<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('research_request_parsed_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('research_request_id')->constrained()->cascadeOnDelete();
            $table->dateTime('uploaded_at');
            $table->longText('results');
            $table->foreignId('media_id')->constrained();
            $table->string('file_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_request_parsed_files');
    }
};
