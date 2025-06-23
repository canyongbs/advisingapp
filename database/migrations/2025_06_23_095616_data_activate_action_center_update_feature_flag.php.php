<?php

use App\Features\ActionCenterUpdateFeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ActionCenterUpdateFeatureFlag::activate();
    }

    public function down(): void
    {
        ActionCenterUpdateFeatureFlag::deactivate();
    }
};
