<?php

use App\Features\QnaAdvisorLinksFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnaAdvisorLinksFeature::activate();
    }

    public function down(): void
    {
        QnaAdvisorLinksFeature::deactivate();
    }
};
