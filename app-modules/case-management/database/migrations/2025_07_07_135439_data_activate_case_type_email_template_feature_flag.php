<?php

use App\Features\CaseTypeEmailTemplateFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CaseTypeEmailTemplateFeature::activate();
    }

    public function down(): void
    {
        CaseTypeEmailTemplateFeature::deactivate();
    }
};
