<?php

use App\Features\EngagementResponseStatusFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EngagementResponseStatusFeature::activate();
    }

    public function down(): void
    {
        EngagementResponseStatusFeature::deactivate();
    }
};
