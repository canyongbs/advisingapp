<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::purge('manage-program-participants');
    }

    public function down(): void
    {
        Feature::activate('manage-program-participants');
    }
};
