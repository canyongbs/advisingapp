<?php

use App\Features\LocalPassword;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        LocalPassword::activate();
    }

    public function down(): void
    {
        LocalPassword::deactivate();
    }
};
