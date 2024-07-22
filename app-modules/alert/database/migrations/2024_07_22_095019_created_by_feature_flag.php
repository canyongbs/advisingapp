<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('alert_created_by');
    }

    public function down(): void
    {
        Feature::deactivate('alert_created_by');
    }
};
