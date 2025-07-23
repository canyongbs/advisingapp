<?php

use App\Features\QnaAdvisorEmbedFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnaAdvisorEmbedFeature::activate();
    }

    public function down(): void
    {
        QnaAdvisorEmbedFeature::deactivate();
    }
};
