<?php

use App\Features\AzureMatchingPropertyFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AzureMatchingPropertyFeature::activate();
    }

    public function down(): void
    {
        AzureMatchingPropertyFeature::deactivate();
    }
};
