<?php

use App\Features\AcademicStandingFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AcademicStandingFeature::activate();
    }

    public function down(): void
    {
        AcademicStandingFeature::deactivate();
    }
};
