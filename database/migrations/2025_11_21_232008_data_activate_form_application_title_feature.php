<?php

use App\Features\FormApplicationTitleFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FormApplicationTitleFeature::activate();
    }

    public function down(): void
    {
        FormApplicationTitleFeature::deactivate();
    }
};
