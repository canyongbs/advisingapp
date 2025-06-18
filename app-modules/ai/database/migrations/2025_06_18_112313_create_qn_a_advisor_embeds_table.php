<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qn_a_advisor_embeds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_enabled')->default(false);
            $table->foreignUuid('qn_a_advisor_id')->constrained('qn_a_advisors')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qn_a_advisor_embeds');
    }
};
