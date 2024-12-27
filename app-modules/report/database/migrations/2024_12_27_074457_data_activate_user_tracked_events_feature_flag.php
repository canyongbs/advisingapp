<?php

use App\Features\UserTrackedEventsFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        UserTrackedEventsFeature::activate();
    }

    public function down(): void
    {
        UserTrackedEventsFeature::deactivate();
    }
};
