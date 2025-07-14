<?php

use App\Features\SettingsPermissons;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SettingsPermissons::activate();
    }

    public function down(): void
    {
        SettingsPermissons::deactivate();
    }
};
