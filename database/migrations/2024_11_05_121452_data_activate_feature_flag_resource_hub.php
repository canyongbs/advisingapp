<?php

use App\Features\ResourceHub;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ResourceHub::activate();
    }

    public function down(): void
    {
        ResourceHub::deactivate();
    }
};
