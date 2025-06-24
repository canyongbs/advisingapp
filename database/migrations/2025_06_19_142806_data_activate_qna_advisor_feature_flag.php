<?php

use App\Features\QnaAdvisorFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnaAdvisorFeature::activate();
    }

    public function down(): void
    {
        QnaAdvisorFeature::deactivate();
    }
};
