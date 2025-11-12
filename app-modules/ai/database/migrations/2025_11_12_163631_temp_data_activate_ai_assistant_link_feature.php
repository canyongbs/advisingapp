<?php

use App\Features\AiAssistantLinkFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AiAssistantLinkFeature::activate();
    }

    public function down(): void
    {
        AiAssistantLinkFeature::deactivate();
    }
};
