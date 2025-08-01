<?php

use App\Features\ProjectComingSoonFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectComingSoonFeature::activate();
    }

    public function down(): void
    {
        ProjectComingSoonFeature::deactivate();
    }
};
