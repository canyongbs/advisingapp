<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
