<?php

use App\Features\BookingGroupFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        BookingGroupFeature::activate();
    }

    public function down(): void
    {
        BookingGroupFeature::deactivate();
    }
};
