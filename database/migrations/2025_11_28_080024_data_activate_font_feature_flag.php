<?php

use App\Features\FontFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FontFeature::activate();
    }

    public function down(): void
    {
        FontFeature::deactivate();
    }
};
