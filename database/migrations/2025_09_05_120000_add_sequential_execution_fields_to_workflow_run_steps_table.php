<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('workflow_run_steps', function (Blueprint $table) {
            // Store the original offset from the workflow step
            $table->integer('offset_minutes')->default(0)->after('details_type');

            // Reference to the previous workflow run step (not the template step)
            $table->foreignUuid('previous_workflow_run_step_id')
                ->nullable()
                ->after('offset_minutes')
                ->constrained('workflow_run_steps')
                ->onDelete('cascade');

            // Make execute_at nullable to support sequential execution
            $table->dateTime('execute_at')->nullable()->change();
        });

        // Create index for efficient querying
        Schema::table('workflow_run_steps', function (Blueprint $table) {
            $table->index(['previous_workflow_run_step_id', 'succeeded_at']);
        });
    }

    public function down(): void
    {
        Schema::table('workflow_run_steps', function (Blueprint $table) {
            $table->dropIndex(['previous_workflow_run_step_id', 'succeeded_at']);
            $table->dropConstrainedForeignId('previous_workflow_run_step_id');
            $table->dropColumn(['offset_minutes']);

            // Revert execute_at back to not nullable
            $table->dateTime('execute_at')->nullable(false)->change();
        });
    }
};
