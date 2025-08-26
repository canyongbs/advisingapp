<?php

use App\Features\Gpt5AndMiniAndNanoFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Gpt5AndMiniAndNanoFeature::activate();
    }

    public function down(): void
    {
        Gpt5AndMiniAndNanoFeature::deactivate();
    }
};
