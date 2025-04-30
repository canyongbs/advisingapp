<?php

use App\Features\OpenAiVersionFeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        OpenAiVersionFeatureFlag::activate();
    }

    public function down(): void
    {
        OpenAiVersionFeatureFlag::deactivate();
    }
};
