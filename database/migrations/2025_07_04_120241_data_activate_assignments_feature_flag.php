<?php

use App\Features\AssignmentsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AssignmentsFeature::activate();
    }

    public function down(): void
    {
        AssignmentsFeature::deactivate();
    }
};
