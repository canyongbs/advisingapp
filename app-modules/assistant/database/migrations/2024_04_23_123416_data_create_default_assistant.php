<?php

use Laravel\Pennant\Feature;
use App\Features\EnableCustomAiAssistants;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate(EnableCustomAiAssistants::class);
    }

    public function down(): void {}
};
