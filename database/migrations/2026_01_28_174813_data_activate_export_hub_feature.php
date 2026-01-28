<?php

use App\Features\ExportHubFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ExportHubFeature::activate();
    }

    public function down(): void
    {
        ExportHubFeature::deactivate();
    }
};
