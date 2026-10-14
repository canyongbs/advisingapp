<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Console;

use AdvisingApp\Ai\Jobs\DispatchAutomaticallyEndCustomerAdvisorsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchDeleteUnsavedAiThreadsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchFetchAiFilesParsingResultsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchUpdateCurrentAiAssistantLinksForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant;
use AdvisingApp\Campaign\Jobs\DispatchExecuteCampaignActionsForEachTenant;
use AdvisingApp\Engagement\Jobs\DispatchDeliverEngagementsForEachTenant;
use AdvisingApp\Engagement\Jobs\DispatchUnmatchedInboundCommunicationsForEachTenant;
use AdvisingApp\Engagement\Jobs\GatherAndDispatchSesS3InboundEmails;
use AdvisingApp\IntegrationAwsSesEventHandling\Jobs\ConsumeSesEventsFromSqs;
use AdvisingApp\IntegrationOpenAi\Jobs\DispatchUploadFilesToVectorStoresForEachTenant;
use AdvisingApp\MeetingCenter\Jobs\DispatchRefreshCalendarRefreshTokensForEachTenant;
use AdvisingApp\MeetingCenter\Jobs\DispatchSyncCalendarsForEachTenant;
use AdvisingApp\Workflow\Jobs\DispatchExecuteWorkflowActionStepsForEachTenant;
use App\Console\Commands\PublishQueueScaleSignalCommand;
use App\Jobs\DispatchHealthChecksForEachTenant;
use App\Jobs\DispatchModelPruningForEachTenant;
use App\Jobs\DispatchStaleCacheTagPruningForEachTenant;
use App\Jobs\PruneQueueMonitorJob;
use App\Models\MonitoredScheduledTaskLogItem;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])
            ->daily()
            ->onOneServer()
            ->monitorName('Landlord Prune MonitoredScheduledTaskLogItems');

        $schedule->job(new GatherAndDispatchSesS3InboundEmails())
            ->everyMinute()
            ->name('Gather and Dispatch SES S3 Inbound Emails')
            ->onOneServer()
            ->monitorName('Gather and Dispatch SES S3 Inbound Emails');

        $schedule->job(new ConsumeSesEventsFromSqs())
            ->everyMinute()
            ->when(fn (): bool => filled(config('services.ses_events_queue.url')))
            ->onOneServer()
            ->monitorName('Consume SES Events From SQS');

        $schedule->job(new DispatchDeliverEngagementsForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Deliver Engagements For Each Tenant');

        $schedule->job(new DispatchExecuteCampaignActionsForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Execute Campaign Actions For Each Tenant');

        $schedule->job(new DispatchExecuteWorkflowActionStepsForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Execute Workflow Action Steps For Each Tenant');

        $schedule->job(new DispatchAutomaticallyEndCustomerAdvisorsForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Automatically End Customer Advisors For Each Tenant');

        $schedule->job(new DispatchFetchAiFilesParsingResultsForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Fetch AI Files Parsing Results For Each Tenant');

        $schedule->job(new DispatchHealthChecksForEachTenant())
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Dispatch Health Checks For Each Tenant');

        $schedule->job(new DispatchSyncCalendarsForEachTenant())
            ->everyFifteenMinutes()
            ->onOneServer()
            ->monitorName('Dispatch Sync Calendars For Each Tenant');

        $schedule->job(new DispatchUploadFilesToVectorStoresForEachTenant())
            ->everyFifteenMinutes()
            ->onOneServer()
            ->monitorName('Dispatch Upload Files To Vector Stores For Each Tenant');

        $schedule->job(new DispatchStaleCacheTagPruningForEachTenant())
            ->hourly()
            ->onOneServer()
            ->monitorName('Dispatch Stale Cache Tag Pruning For Each Tenant');

        $schedule->job(new DispatchUnmatchedInboundCommunicationsForEachTenant())
            ->daily()
            ->onOneServer()
            ->monitorName('Dispatch Unmatched Inbound Communications For Each Tenant');

        $schedule->job(new DispatchDeleteUnsavedAiThreadsForEachTenant())
            ->daily()
            ->onOneServer()
            ->monitorName('Dispatch Delete Unsaved AI Threads For Each Tenant');

        $schedule->job(new DispatchModelPruningForEachTenant())
            ->daily()
            ->onOneServer()
            ->monitorName('Dispatch Model Pruning For Each Tenant');

        $schedule->job(new DispatchRefreshCalendarRefreshTokensForEachTenant())
            ->daily()
            ->onOneServer()
            ->monitorName('Dispatch Refresh Calendar Refresh Tokens For Each Tenant');

        $schedule->job(new DispatchUpdateCurrentAiAssistantLinksForEachTenant())
            ->monthlyOn(1, '0:0')
            ->onOneServer()
            ->monitorName('Dispatch Update Current AI Assistant Links For Each Tenant');

        $schedule->job(new DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant())
            ->monthlyOn(1, '0:0')
            ->onOneServer()
            ->monitorName('Dispatch Update Current Customer Advisor Links For Each Tenant');

        $schedule->command('health:queue-check-heartbeat')
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Queue Check Heartbeat');

        $schedule->command('health:schedule-check-heartbeat')
            ->everyMinute()
            ->onOneServer()
            ->monitorName('Schedule Check Heartbeat');

        // Feeds ECS scaling of the worker service, so it is only published from ECS.
        $schedule->command(PublishQueueScaleSignalCommand::class)
            ->everyMinute()
            ->when(fn (): bool => filled(config('app.ecs_agent_uri')))
            ->onOneServer()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->monitorName('Publish Queue Scale Signal');

        $schedule->job(new PruneQueueMonitorJob())
            ->everyFiveMinutes()
            ->onOneServer()
            ->monitorName('Prune Queue Monitor');

        // The queue metrics package schedules these itself without onOneServer(), so its scheduling is turned off.
        $schedule->command('queue-metrics:cleanup-stale-workers', ['--threshold' => config('queue-metrics.worker_heartbeat.stale_threshold')])
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->monitorName('Queue Metrics Cleanup Stale Workers');

        $schedule->command('queue-metrics:calculate')
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->monitorName('Queue Metrics Calculate');

        $schedule->command('queue-metrics:record-trends')
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->monitorName('Queue Metrics Record Trends');

        // The package's shortest baseline interval; it recalculates less often only once a baseline is confident.
        $schedule->command('queue-metrics:calculate-baselines')
            ->everyFiveMinutes()
            ->onOneServer()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->monitorName('Queue Metrics Calculate Baselines');

        // Registered last so it only records once a full schedule run has been dispatched; per-node, so never onOneServer().
        $schedule->call(fn () => touch(storage_path('framework/schedule-heartbeat')))
            ->everyMinute()
            ->name('Schedule Liveness Beacon')
            ->doNotMonitor()
            ->sentryMonitor(monitorSlug: 'advisingapp-scheduler-liveness', checkInMargin: 5, failureIssueThreshold: 5);
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
