<?php

use App\Features\QnaAdvisorCardViewFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnaAdvisorCardViewFeature::activate();
    }

    public function down(): void
    {
        QnaAdvisorCardViewFeature::deactivate();
    }
};
