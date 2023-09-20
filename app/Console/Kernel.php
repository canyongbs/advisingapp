<?php

namespace App\Console;

use Assist\Audit\Models\Audit;
use App\Models\FailedImportRow;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Assist\Assistant\Models\AssistantChatMessageLog;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('cache:prune-stale-tags')->hourly();

        $schedule->command(RunHealthChecksCommand::class)->everyMinute();

        $schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();

        $schedule->command(PruneCommand::class, [
            '--model' => [Audit::class, AssistantChatMessageLog::class, FailedImportRow::class],
        ])->daily()->evenInMaintenanceMode()->onOneServer();

        // Needs to remain as the last command: https://spatie.be/docs/laravel-health/v1/available-checks/schedule
        $schedule->command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
