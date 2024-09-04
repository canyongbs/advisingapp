<?php

use App\Enums\FeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FeatureFlag::ApplicationName->activate();
    }

    public function down(): void
    {
        FeatureFlag::ApplicationName->deactivate();
    }
};
