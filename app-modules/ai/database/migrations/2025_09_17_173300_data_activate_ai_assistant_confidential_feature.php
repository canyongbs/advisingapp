<?php

use App\Features\AiAssistantConfidentialFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AiAssistantConfidentialFeature::activate();
    }

    public function down(): void
    {
        AiAssistantConfidentialFeature::deactivate();
    }
};
