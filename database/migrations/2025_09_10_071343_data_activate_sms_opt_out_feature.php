<?php

use App\Features\SmsOptOutFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SmsOptOutFeature::activate();
    }

    public function down(): void
    {
        SmsOptOutFeature::deactivate();
    }
};
