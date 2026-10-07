<?php

use App\Features\SesEventDeduplicationFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SesEventDeduplicationFeature::activate();
    }

    public function down(): void
    {
        SesEventDeduplicationFeature::deactivate();
    }
};
