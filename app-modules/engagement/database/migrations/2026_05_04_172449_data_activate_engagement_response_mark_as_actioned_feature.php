<?php

use App\Features\EngagementResponseMarkAsActionedFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EngagementResponseMarkAsActionedFeature::activate();
    }

    public function down(): void
    {
        EngagementResponseMarkAsActionedFeature::deactivate();
    }
};
