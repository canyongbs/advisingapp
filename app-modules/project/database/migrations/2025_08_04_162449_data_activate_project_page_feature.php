<?php

use App\Features\ProjectPageFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectPageFeature::activate();
    }

    public function down(): void
    {
        ProjectPageFeature::deactivate();
    }
};
