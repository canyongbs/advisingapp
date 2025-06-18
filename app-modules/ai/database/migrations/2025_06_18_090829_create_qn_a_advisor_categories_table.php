<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('qn_a_advisor_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description');
            $table->foreignUuid('qn_a_advisor_id')->constrained('qn_a_advisors')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['qn_a_advisor_id', 'name'], 'uq_qn_a_advisor_categories_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qn_a_advisor_categories');
    }
};
