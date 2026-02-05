<?php

use App\Features\FormRepeaterFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FormRepeaterFeature::activate();
    }

    public function down(): void
    {
        FormRepeaterFeature::deactivate();
    }
};
