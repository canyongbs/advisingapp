<?php

use App\Features\ReportingFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ReportingFeature::activate();
    }

    public function down(): void
    {
        ReportingFeature::deactivate();
    }
};
