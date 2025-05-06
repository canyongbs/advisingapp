<?php

use App\Features\GPTO4MiniFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        GPTO4MiniFeature::activate();
    }

    public function down(): void
    {
        GPTO4MiniFeature::deactivate();
    }
};
