<?php

use App\Features\CaseTypeNotificationFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CaseTypeNotificationFeature::activate();
    }

    public function down(): void
    {
        CaseTypeNotificationFeature::deactivate();
    }
};
