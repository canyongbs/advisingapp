<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('qna_advisor_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('advisor_id')->constrained('qna_advisors');
            $table->nullableUuidMorphs('author');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qna_advisor_threads');
    }
};
