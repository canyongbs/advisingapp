<?php

use App\Features\JourneyStepPermissionRename;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        JourneyStepPermissionRename::activate();
    }

    public function down(): void
    {
        JourneyStepPermissionRename::deactivate();
    }
};
