<?php

use Illuminate\Database\Migrations\Migration;

use App\Features\AddBrandedWebsitesToThemeSettingsFeature;

return new class () extends Migration {
    public function up(): void
    {
        AddBrandedWebsitesToThemeSettingsFeature::activate();
    }

    public function down(): void
    {
        AddBrandedWebsitesToThemeSettingsFeature::deactivate();
    }
};