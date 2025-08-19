<?php

use App\Features\CatalogYearFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CatalogYearFeature::activate();
    }

    public function down(): void
    {
        CatalogYearFeature::deactivate();
    }
};
