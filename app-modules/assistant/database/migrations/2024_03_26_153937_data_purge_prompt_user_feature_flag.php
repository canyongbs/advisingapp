<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::purge('prompt-user');
    }

    public function down(): void
    {
        Feature::activate('prompt-user');
    }
};
