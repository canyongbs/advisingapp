<?php

use App\Features\ArchiveSubmissionsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ArchiveSubmissionsFeature::activate();
    }

    public function down(): void
    {
        ArchiveSubmissionsFeature::deactivate();
    }
};
