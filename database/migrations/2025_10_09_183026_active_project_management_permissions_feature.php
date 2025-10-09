<?php

use App\Features\ProjectManagementPermissionsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectManagementPermissionsFeature::activate();
    }

    public function down(): void
    {
        ProjectManagementPermissionsFeature::purge();
    }
};
