<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Throwable;
use App\Models\Tenant;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Audit\Models\Audit;
use Illuminate\Support\Facades\Log;
use AdvisingApp\Ai\Models\AiMessage;
use App\Models\Scopes\SetupIsComplete;
use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Console\Scheduling\Schedule;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Engagement\Models\EngagementFile;
use Filament\Actions\Imports\Models\FailedImportRow;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use AdvisingApp\MeetingCenter\Console\Commands\RefreshCalendarRefreshTokens;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        Tenant::query()
            ->tap(new SetupIsComplete())
            ->cursor()
            ->each(function (Tenant $tenant) use ($schedule) {
                try {
                    $schedule->command("tenants:artisan \"cache:prune-stale-tags\" --tenant={$tenant->id}")
                        ->hourly()
                        ->onOneServer()
                        ->withoutOverlapping();

                    $schedule->command("tenants:artisan \"health:check\" --tenant={$tenant->id}")
                        ->everyMinute()
                        ->onOneServer()
                        ->withoutOverlapping();

                    $schedule->command("tenants:artisan \"health:queue-check-heartbeat\" --tenant={$tenant->id}")
                        ->everyMinute()
                        ->onOneServer()
                        ->withoutOverlapping();

                    $schedule->command("ai:delete-unsaved-ai-threads --tenant={$tenant->id}")
                        ->daily()
                        ->onOneServer()
                        ->withoutOverlapping();

                    collect([
                        AiMessageFile::class,
                        AiMessage::class,
                        AiThread::class,
                        Audit::class,
                        EngagementFile::class,
                        FailedImportRow::class,
                        FormAuthentication::class,
                    ])
                        ->each(
                            fn ($model) => $schedule->command("tenants:artisan \"model:prune --model={$model}\" --tenant={$tenant->id}")
                                ->daily()
                                ->onOneServer()
                                ->withoutOverlapping()
                        );

                    $schedule->command(
                        command: RefreshCalendarRefreshTokens::class,
                        parameters: [
                            "--tenant={$tenant->id}",
                        ]
                    )
                        ->daily()
                        ->onOneServer()
                        ->withoutOverlapping();

                    $schedule->command("tenants:artisan \"health:schedule-check-heartbeat\" --tenant={$tenant->id}")
                        ->name("health:schedule-check-heartbeat-{$tenant->id}")
                        ->everyMinute()
                        ->onOneServer();
                } catch (Throwable $th) {
                    Log::error('Error scheduling tenant commands.', [
                        'tenant' => $tenant->id,
                        'exception' => $th,
                    ]);

                    report($th);
                }
            });
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
