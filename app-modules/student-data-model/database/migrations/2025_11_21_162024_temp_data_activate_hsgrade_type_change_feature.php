<?php

use App\Features\HsGradeTypeChangeFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        HsGradeTypeChangeFeature::activate();
    }

    public function down(): void
    {
        HsGradeTypeChangeFeature::deactivate();
    }
};
