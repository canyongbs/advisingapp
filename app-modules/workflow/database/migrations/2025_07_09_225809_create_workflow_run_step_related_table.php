<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_run_step_related', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('workflow_run_id')->constrained('workflow_runs');
            $table->uuidMorphs('related');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_run_step_related');
    }
};
