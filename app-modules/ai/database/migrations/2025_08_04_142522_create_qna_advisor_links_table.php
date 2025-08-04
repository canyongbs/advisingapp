<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('qna_advisor_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('advisor_id')->constrained('qna_advisors')->cascadeOnDelete();
            $table->string('url');
            $table->longText('parsing_results')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qna_advisor_links');
    }
};
