<?php

namespace App\Console;

use Assist\Audit\Models\Audit;
use App\Models\FailedImportRow;
use Illuminate\Console\Scheduling\Schedule;
use Assist\Engagement\Models\EngagementFile;
use Illuminate\Database\Console\PruneCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use App\Console\Commands\RefreshAdmMaterializedView;
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
        if (config('database.adm_materialized_views_enabled')) {
            $this->refreshAdmMaterializedViews($schedule);
        }

        $schedule->command('cache:prune-stale-tags')->hourly();

        $schedule->command(RunHealthChecksCommand::class)->everyMinute();

        $schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();

        collect([
            Audit::class,
            AssistantChatMessageLog::class,
            FailedImportRow::class,
            EngagementFile::class,
        ])
            ->each(
                fn ($model) => $schedule->command(PruneCommand::class, [
                    '--model' => [$model],
                ])
                    ->daily()
                    ->onOneServer()
                    ->runInBackground()
            );

        // Needs to remain as the last command: https://spatie.be/docs/laravel-health/v1/available-checks/schedule
        $schedule->command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
    }

    protected function refreshAdmMaterializedViews(Schedule $schedule): void
    {
        $schedule->command(RefreshAdmMaterializedView::class, ['students'])
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(RefreshAdmMaterializedView::class, ['enrollments'])
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(RefreshAdmMaterializedView::class, ['performance'])
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(RefreshAdmMaterializedView::class, ['programs'])
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();
    }

    protected function pruning(Schedule $schedule) {}

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
