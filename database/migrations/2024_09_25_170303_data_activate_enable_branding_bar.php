<?php

use App\Features\EnableBrandingBar;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EnableBrandingBar::activate();
    }

    public function down(): void
    {
        EnableBrandingBar::deactivate();
    }
};
