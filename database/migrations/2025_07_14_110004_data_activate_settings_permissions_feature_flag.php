<?php

use App\Features\SettingsPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SettingsPermissions::activate();
    }

    public function down(): void
    {
        SettingsPermissions::deactivate();
    }
};
