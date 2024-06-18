<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('introduce-multifactor-authentication');
    }

    public function down(): void
    {
        Feature::deactivate('introduce-multifactor-authentication');
    }
};
