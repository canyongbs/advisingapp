<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Console;

use AdvisingApp\Audit\Models\Audit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Engagement\Models\EngagementFile;
use Spatie\Health\Commands\RunHealthChecksCommand;
use App\Console\Commands\RefreshAdmMaterializedView;
use AdvisingApp\Assistant\Models\AssistantChatMessageLog;
use Filament\Actions\Imports\Models\FailedImportRow;
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
            EngagementFile::class,
            FailedImportRow::class,
            FormAuthentication::class,
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
