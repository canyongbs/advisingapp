<?php

use App\Features\TagFeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        TagFeatureFlag::activate();
    }

    public function down(): void
    {
        TagFeatureFlag::deactivate();
    }
};
