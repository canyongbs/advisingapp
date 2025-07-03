<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_interaction_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('interaction_initiative_id')->nullable()->constrained('interaction_initiatives');
            $table->foreignUuid('interaction_driver_id')->nullable()->constrained('interaction_drivers');
            $table->foreignUuid('division_id')->nullable()->constrained('divisions');
            $table->foreignUuid('interaction_outcome_id')->nullable()->constrained('interaction_outcomes');
            $table->foreignUuid('interaction_relation_id')->nullable()->constrained('interaction_relations');
            $table->foreignUuid('interaction_status_id')->nullable()->constrained('interaction_statuses');
            $table->foreignUuid('interaction_type_id')->nullable()->constrained('interaction_types');

            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();

            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_interaction_details');
    }
};
