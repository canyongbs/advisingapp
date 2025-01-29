<?php

use App\Features\ConfidentialInteractionFeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ConfidentialInteractionFeatureFlag::activate();
    }

    public function down(): void
    {
        ConfidentialInteractionFeatureFlag::deactivate();
    }
};
