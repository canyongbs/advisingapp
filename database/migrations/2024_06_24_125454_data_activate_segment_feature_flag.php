<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('enable-segments');
    }

    public function down(): void
    {
        Feature::deactivate('enable-segments');
    }
};
