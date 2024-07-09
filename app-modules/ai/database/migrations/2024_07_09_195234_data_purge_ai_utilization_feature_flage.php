<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::purge('ai_utilization');
    }

    public function down(): void
    {
        Feature::activate('ai_utilization');
    }
};
