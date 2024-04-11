<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('educatable-alerts-timeline');
    }

    public function down(): void
    {
        Feature::deactivate('educatable-alerts-timeline');
    }
};
