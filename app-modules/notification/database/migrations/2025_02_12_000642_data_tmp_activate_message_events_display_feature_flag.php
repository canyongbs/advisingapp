<?php

use App\Features\MessageEventsDisplay;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        MessageEventsDisplay::activate();
    }

    public function down(): void
    {
        MessageEventsDisplay::deactivate();
    }
};
