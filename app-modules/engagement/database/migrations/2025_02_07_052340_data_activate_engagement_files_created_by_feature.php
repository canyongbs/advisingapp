<?php

use App\Features\EngagementFilesCreatedByFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EngagementFilesCreatedByFeature::activate();
    }

    public function down(): void
    {
        EngagementFilesCreatedByFeature::deactivate();
    }
};
