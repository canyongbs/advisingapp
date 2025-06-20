<?php

use App\Features\QnAAdvisorFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        QnAAdvisorFeature::activate();
    }

    public function down(): void
    {
        QnAAdvisorFeature::deactivate();
    }
};
