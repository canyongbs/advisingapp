<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('notification-settings-from-name');
    }

    public function down(): void
    {
        Feature::deactivate('notification-settings-from-name');
    }
};
