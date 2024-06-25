<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('segment-as-caseload-replacement');
    }

    public function down(): void
    {
        Feature::deactivate('segment-as-caseload-replacement');
    }
};
