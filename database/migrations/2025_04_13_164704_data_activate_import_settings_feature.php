<?php

use App\Features\ImportSettingsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ImportSettingsFeature::activate();
    }

    public function down(): void
    {
        ImportSettingsFeature::deactivate();
    }
};
