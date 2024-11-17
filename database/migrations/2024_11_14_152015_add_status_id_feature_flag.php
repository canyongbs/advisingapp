<?php

use Illuminate\Database\Migrations\Migration;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('alert_status_id');
    }

    public function down(): void
    {
        Feature::deactivate('alert_status_id');
    }
};
