<?php

use App\Features\ProjectPipelinesFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectPipelinesFeature::activate();
    }

    public function down(): void
    {
        ProjectPipelinesFeature::deactivate();
    }
};
