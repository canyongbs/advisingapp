<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::purge('basic-needs');
    }

    public function down(): void
    {
        Feature::activate('basic-needs');
    }
};
