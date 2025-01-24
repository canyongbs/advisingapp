<?php

use App\Features\ScheduleMonitor;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ScheduleMonitor::activate();
    }

    public function down(): void
    {
        ScheduleMonitor::deactivate();
    }
};
