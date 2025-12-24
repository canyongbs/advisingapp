<?php

use App\Features\EventTransparencyFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EventTransparencyFeature::activate();
    }

    public function down(): void
    {
        EventTransparencyFeature::deactivate();
    }
};
