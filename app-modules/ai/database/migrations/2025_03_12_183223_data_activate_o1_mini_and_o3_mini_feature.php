<?php

use App\Features\O1MiniAndO3MiniFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        O1MiniAndO3MiniFeature::activate();
    }

    public function down(): void
    {
        O1MiniAndO3MiniFeature::deactivate();
    }
};
