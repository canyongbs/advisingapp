<?php

use App\Features\AthleticFieldsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AthleticFieldsFeature::activate();
    }

    public function down(): void
    {
        AthleticFieldsFeature::deactivate();
    }
};
