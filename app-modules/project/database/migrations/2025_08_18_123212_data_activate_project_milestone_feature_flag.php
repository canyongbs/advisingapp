<?php

use App\Features\ProjectMilestoneFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectMilestoneFeature::activate();
    }

    public function down(): void
    {
        ProjectMilestoneFeature::deactivate();
    }
};
