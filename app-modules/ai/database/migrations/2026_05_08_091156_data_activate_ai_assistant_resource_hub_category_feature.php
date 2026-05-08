<?php

use App\Features\AiAssistantResourceHubCategoryFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AiAssistantResourceHubCategoryFeature::activate();
    }

    public function down(): void
    {
        AiAssistantResourceHubCategoryFeature::deactivate();
    }
};
