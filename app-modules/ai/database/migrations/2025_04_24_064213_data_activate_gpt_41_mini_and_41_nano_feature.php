<?php

use App\Features\GPT41MiniAnd41NanoFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        GPT41MiniAnd41NanoFeature::activate();
    }

    public function down(): void
    {
        GPT41MiniAnd41NanoFeature::deactivate();
    }
};
