<?php

use App\Features\SmartPromptsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SmartPromptsFeature::activate();
    }

    public function down(): void
    {
        SmartPromptsFeature::deactivate();
    }
};
