<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('manage-program-participants');
    }

    public function down(): void
    {
        Feature::deactivate('manage-program-participants');
    }
};
