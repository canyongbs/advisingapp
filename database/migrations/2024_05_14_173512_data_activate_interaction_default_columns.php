<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('interaction_initiative_default');
        Feature::activate('interaction_driver_default');
        Feature::activate('interaction_type_default');
    }

    public function down(): void
    {
        Feature::deactivate('interaction_initiative_default');
        Feature::deactivate('interaction_driver_default');
        Feature::deactivate('interaction_type_default');
    }
};
