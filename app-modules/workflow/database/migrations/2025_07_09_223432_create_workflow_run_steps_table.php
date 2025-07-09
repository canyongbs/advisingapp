<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_run_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->dateTime('executed_at')->nullable();
            $table->dateTime('dispatched_at')->nullable();
            $table->dateTime('succeeded_at')->nullable();
            $table->dateTime('last_failed_at')->nullable();
            $table->foreignUuid('workflow_run_step_id')->constrained('workflow_run_steps');
            $table->uuidMorphs('details');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_run_steps');
    }
};
