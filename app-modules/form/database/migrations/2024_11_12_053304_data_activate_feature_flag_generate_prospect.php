<?php

use App\Features\GenerateProspectFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        GenerateProspectFeature::activate();
    }

    public function down(): void
    {
        GenerateProspectFeature::deactivate();
    }
};
