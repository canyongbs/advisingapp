<?php

use App\Features\ApplicationSubmissionChecklistFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ApplicationSubmissionChecklistFeature::activate();
    }

    public function down(): void
    {
        ApplicationSubmissionChecklistFeature::deactivate();
    }
};
