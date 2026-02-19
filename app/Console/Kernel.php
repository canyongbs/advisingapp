<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Jobs\QnaAdvisors\AutomaticallyEndQnaAdvisors;
use AdvisingApp\Ai\Jobs\QnaAdvisors\UpdateCurrentQnaAdvisorLinks;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Audit\Models\Audit;
use AdvisingApp\Authorization\Models\LoginMagicLink;
use AdvisingApp\Campaign\Jobs\ExecuteCampaignActions;
use AdvisingApp\Engagement\Jobs\DeliverEngagements as DeliverEngagementsJob;
use AdvisingApp\Engagement\Jobs\GatherAndDispatchSesS3InboundEmails;
use AdvisingApp\Engagement\Jobs\UnmatchedInboundCommunicationsJob;
use AdvisingApp\Engagement\Models\EngagementFile;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\MeetingCenter\Console\Commands\RefreshCalendarRefreshTokens;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendars;
use AdvisingApp\Project\Models\ProjectFile;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionStepsJob;
use App\Models\HealthCheckResultHistoryItem;
use App\Models\MonitoredScheduledTaskLogItem;
use App\Models\Scopes\SetupIsComplete;
use App\Models\Tenant;
use Filament\Actions\Imports\Models\FailedImportRow;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Throwable;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])
            ->daily()
            ->withoutOverlapping(720)
            ->monitorName('Landlord Prune MonitoredScheduledTaskLogItems');

        $schedule->job(new GatherAndDispatchSesS3InboundEmails())
            ->everyMinute()
            ->name('Gather and Dispatch SES S3 Inbound Emails')
            ->monitorName('Gather and Dispatch SES S3 Inbound Emails');

        Tenant::query()
            ->tap(new SetupIsComplete())
            ->cursor()
            ->each(function (Tenant $tenant) use ($schedule) {
                try {
                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(app(DeliverEngagementsJob::class));
                        });
                    })
                        ->everyMinute()
                        ->name("Dispatch DeliverEngagements | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch DeliverEngagements | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new UnmatchedInboundCommunicationsJob());
                        });
                    })
                        ->daily()
                        ->name("Process Unmatched Inbound Communications | Tenant {$tenant->domain}")
                        ->monitorName("Process Unmatched Inbound Communications | Tenant {$tenant->domain}")
                        ->withoutOverlapping(720);

                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new SyncCalendars());
                        });
                    })
                        ->everyFifteenMinutes()
                        ->name("Dispatch SyncCalendars | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch SyncCalendars | Tenant {$tenant->domain}")
                        ->withoutOverlapping(60);

                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new ExecuteCampaignActions());
                        });
                    })
                        ->everyMinute()
                        ->name("Dispatch ExecuteCampaignActions | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch ExecuteCampaignActions | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new ExecuteWorkflowActionStepsJob());
                        });
                    })
                        ->everyMinute()
                        ->name("Dispatch ExecuteWorkflowActionStepsJob | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch ExecuteWorkflowActionStepsJob | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new AutomaticallyEndQnaAdvisors());
                        });
                    })
                        ->everyMinute()
                        ->name("Dispatch AutomaticallyEndQnaAdvisors | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch AutomaticallyEndQnaAdvisors | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);
                    
                    $schedule->call(function () use ($tenant) {
                        $tenant->execute(function () {
                            dispatch(new UpdateCurrentQnaAdvisorLinks());
                        });
                    })
                        ->monthlyOn(1, '0:0')
                        ->name("Dispatch UpdateCurrentQnaAdvisorLinks | Tenant {$tenant->domain}")
                        ->monitorName("Dispatch UpdateCurrentQnaAdvisorLinks | Tenant {$tenant->domain}")
                        ->withoutOverlapping(60);

                    $schedule->command("tenants:artisan \"cache:prune-stale-tags\" --tenant={$tenant->id}")
                        ->hourly()
                        ->name("Prune Stale Cache Tags | Tenant {$tenant->domain}")
                        ->monitorName("Prune Stale Cache Tags | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->command("tenants:artisan \"health:queue-check-heartbeat\" --tenant={$tenant->id}")
                        ->everyMinute()
                        ->name("Queue Check Heartbeat | Tenant {$tenant->domain}")
                        ->monitorName("Queue Check Heartbeat | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->command("ai:fetch-files-parsing-results --tenant={$tenant->id}")
                        ->everyMinute()
                        ->name("Fetch AI Assistant Files Parsed Results | Tenant {$tenant->domain}")
                        ->monitorName("Fetch AI Assistant Files Parsed Results | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->command("ai:delete-unsaved-ai-threads --tenant={$tenant->id}")
                        ->daily()
                        ->name("Delete Unsaved AI Threads | Tenant {$tenant->domain}")
                        ->monitorName("Delete Unsaved AI Threads | Tenant {$tenant->domain}")
                        ->withoutOverlapping(720);

                    $schedule->command("integration-open-ai:upload-files-to-vector-stores --tenant={$tenant->id}")
                        ->everyFifteenMinutes()
                        ->name("Upload AI Assistant Files To Open AI Vector Stores | Tenant {$tenant->domain}")
                        ->monitorName("Upload AI Assistant Files To Open AI Vector Stores | Tenant {$tenant->domain}")
                        ->withoutOverlapping(60);

                    $modelsToPrune = collect([
                        AiMessageFile::class,
                        AiMessage::class,
                        AiThread::class,
                        Audit::class,
                        EngagementFile::class,
                        FailedImportRow::class,
                        FormAuthentication::class,
                        HealthCheckResultHistoryItem::class,
                        ProjectFile::class,
                        LoginMagicLink::class,
                    ])
                        ->join(',');

                    $schedule->command("tenants:artisan \"model:prune --model={$modelsToPrune}\" --tenant={$tenant->id}")
                        ->daily()
                        ->name("Prune Models | Tenant {$tenant->domain}")
                        ->monitorName("Prune Models | Tenant {$tenant->domain}")
                        ->withoutOverlapping(720);

                    $schedule->command(
                        command: RefreshCalendarRefreshTokens::class,
                        parameters: [
                            "--tenant={$tenant->id}",
                        ]
                    )
                        ->daily()
                        ->name("Refresh Calendar Refresh Tokens | Tenant {$tenant->domain}")
                        ->monitorName("Refresh Calendar Refresh Tokens | Tenant {$tenant->domain}")
                        ->withoutOverlapping(720);

                    $schedule->command("tenants:artisan \"prospect:prune-eductable-pipeline-stages\" --tenant={$tenant->id}")
                        ->daily()
                        ->name("Prune Educatable Pipeline Stages | Tenant {$tenant->domain}")
                        ->monitorName("Prune Educatable Pipeline Stages | Tenant {$tenant->domain}")
                        ->withoutOverlapping(720);

                    $schedule->command("tenants:artisan \"health:check\" --tenant={$tenant->id}")
                        ->everyMinute()
                        ->name("Health Check | Tenant {$tenant->domain}")
                        ->monitorName("Health Check | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);

                    $schedule->command("tenants:artisan \"health:schedule-check-heartbeat\" --tenant={$tenant->id}")
                        ->everyMinute()
                        ->name("Schedule Check Heartbeat | Tenant {$tenant->domain}")
                        ->monitorName("Schedule Check Heartbeat | Tenant {$tenant->domain}")
                        ->withoutOverlapping(15);
                } catch (Throwable $exception) {
                    Log::error('Error scheduling tenant commands.', [
                        'tenant' => $tenant->id,
                        'exception' => $exception,
                    ]);

                    report($exception);
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
