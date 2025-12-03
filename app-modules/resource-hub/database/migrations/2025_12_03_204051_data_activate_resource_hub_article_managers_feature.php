<?php

use App\Features\ResourceHubArticleManagersFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ResourceHubArticleManagersFeature::activate();
    }

    public function down(): void
    {
        ResourceHubArticleManagersFeature::deactivate();
    }
};
