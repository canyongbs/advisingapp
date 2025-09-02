<?php

use App\Features\InteractionMetadataFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        InteractionMetadataFeature::activate();
    }

    public function down(): void
    {
        InteractionMetadataFeature::deactivate();
    }
};
