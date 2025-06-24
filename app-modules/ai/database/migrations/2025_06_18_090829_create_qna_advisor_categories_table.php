<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('qna_advisor_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description');
            $table->foreignUuid('qna_advisor_id')->constrained('qna_advisors')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['qna_advisor_id', 'name'], 'qna_advisor_categories_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qna_advisor_categories');
    }
};
