<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('setup-complete');
    }

    public function down(): void
    {
        Feature::deactivate('setup-complete');
    }
};
