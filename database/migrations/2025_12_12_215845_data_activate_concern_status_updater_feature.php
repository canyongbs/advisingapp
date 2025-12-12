<?php

use App\Features\ConcernStatusUpdaterFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ConcernStatusUpdaterFeature::activate();
    }

    public function down(): void
    {
        ConcernStatusUpdaterFeature::deactivate();
    }
};
