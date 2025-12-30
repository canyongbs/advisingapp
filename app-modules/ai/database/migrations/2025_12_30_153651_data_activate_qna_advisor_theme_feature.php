<?php

use App\Features\QnaAdvisorThemeFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnaAdvisorThemeFeature::activate();
    }

    public function down(): void
    {
        QnaAdvisorThemeFeature::deactivate();
    }
};
