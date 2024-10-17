<?php

use App\Features\ProspectConversion;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProspectConversion::activate();
    }

    public function down(): void
    {
        ProspectConversion::deactivate();
    }
};
