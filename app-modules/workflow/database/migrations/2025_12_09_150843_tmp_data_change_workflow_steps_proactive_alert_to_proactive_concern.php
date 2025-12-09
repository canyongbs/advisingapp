<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('workflow_steps')
                ->where('current_details_type', 'workflow_proactive_alert_details')
                ->chunkById(100, function (Collection $workflowSteps) {
                    foreach ($workflowSteps as $workflowStep) {
                        DB::table('workflow_steps')
                            ->where('id', $workflowStep->id)
                            ->update(['current_details_type' => 'workflow_proactive_concern_details']);
                    }
                });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('workflow_steps')
                ->where('current_details_type', 'workflow_proactive_concern_details')
                ->chunkById(100, function (Collection $workflowSteps) {
                    foreach ($workflowSteps as $workflowStep) {
                        DB::table('workflow_steps')
                            ->where('id', $workflowStep->id)
                            ->update(['current_details_type' => 'workflow_proactive_alert_details']);
                    }
                });
        });
    }
};
