<?php

use App\Features\WorkflowFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        WorkflowFeature::activate();
    }

    public function down(): void
    {
        WorkflowFeature::deactivate();
    }
};
