<?php

use App\Features\AddCreatedByFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AddCreatedByFeature::activate();
    }

    public function down(): void
    {
        AddCreatedByFeature::deactivate();
    }
};
