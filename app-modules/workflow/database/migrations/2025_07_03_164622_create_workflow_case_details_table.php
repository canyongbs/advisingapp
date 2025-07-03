<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_case_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('division_id')->constrained('divisions');
            $table->foreignUuid('status_id')->constrained('case_statuses');
            $table->foreignUuid('priority_id')->constrained('case_priorities');
            $table->foreignUuid('assigned_to_id')->nullable()->constrained('users');
            $table->longText('close_details');
            $table->longText('res_details');
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_case_details');
    }
};
