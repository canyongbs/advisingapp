<?php

use App\Features\ProjectFileFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectFileFeature::activate();
    }

    public function down(): void
    {
        ProjectFileFeature::deactivate();
    }
};
