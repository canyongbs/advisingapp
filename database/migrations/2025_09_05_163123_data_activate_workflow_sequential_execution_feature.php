<?php

use App\Features\WorkflowSequentialExecutionFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        WorkflowSequentialExecutionFeature::activate();
    }

    public function down(): void
    {
        WorkflowSequentialExecutionFeature::deactivate();
    }
};
