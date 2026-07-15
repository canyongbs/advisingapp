<?php

use App\Features\EmployeeAdvisorPreviewFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EmployeeAdvisorPreviewFeature::activate();
    }

    public function down(): void
    {
        EmployeeAdvisorPreviewFeature::deactivate();
    }
};
