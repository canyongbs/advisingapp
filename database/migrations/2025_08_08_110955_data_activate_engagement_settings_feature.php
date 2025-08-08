<?php

use App\Features\EngagementSettingsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EngagementSettingsFeature::activate();
    }

    public function down(): void
    {
        EngagementSettingsFeature::deactivate();
    }
};
