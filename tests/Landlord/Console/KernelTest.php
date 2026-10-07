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

use AdvisingApp\Ai\Jobs\DispatchAutomaticallyEndCustomerAdvisorsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchDeleteUnsavedAiThreadsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchFetchAiFilesParsingResultsForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchUpdateCurrentAiAssistantLinksForEachTenant;
use AdvisingApp\Ai\Jobs\DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant;
use AdvisingApp\Campaign\Jobs\DispatchExecuteCampaignActionsForEachTenant;
use AdvisingApp\Engagement\Jobs\DispatchDeliverEngagementsForEachTenant;
use AdvisingApp\Engagement\Jobs\DispatchUnmatchedInboundCommunicationsForEachTenant;
use AdvisingApp\Engagement\Jobs\GatherAndDispatchSesS3InboundEmails;
use AdvisingApp\IntegrationOpenAi\Jobs\DispatchUploadFilesToVectorStoresForEachTenant;
use AdvisingApp\MeetingCenter\Jobs\DispatchRefreshCalendarRefreshTokensForEachTenant;
use AdvisingApp\MeetingCenter\Jobs\DispatchSyncCalendarsForEachTenant;
use AdvisingApp\Workflow\Jobs\DispatchExecuteWorkflowActionStepsForEachTenant;
use App\Jobs\DispatchHealthChecksForEachTenant;
use App\Jobs\DispatchModelPruningForEachTenant;
use App\Jobs\DispatchStaleCacheTagPruningForEachTenant;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\artisan;
use function Pest\Laravel\travelTo;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

function assertEveryMinuteTasksPushed(): void
{
    Queue::assertPushed(GatherAndDispatchSesS3InboundEmails::class);
    Queue::assertPushed(DispatchDeliverEngagementsForEachTenant::class);
    Queue::assertPushed(DispatchExecuteCampaignActionsForEachTenant::class);
    Queue::assertPushed(DispatchExecuteWorkflowActionStepsForEachTenant::class);
    Queue::assertPushed(DispatchAutomaticallyEndCustomerAdvisorsForEachTenant::class);
    Queue::assertPushed(DispatchFetchAiFilesParsingResultsForEachTenant::class);
    Queue::assertPushed(DispatchHealthChecksForEachTenant::class);
}

describe('schedule', function () {
    it('dispatches the tenant fan-out orchestrators on a daily run', function () {
        Queue::fake();

        travelTo(now()->startOfMonth()->addDays(14)->startOfDay());

        artisan('schedule:run');

        assertEveryMinuteTasksPushed();

        Queue::assertPushed(DispatchSyncCalendarsForEachTenant::class);
        Queue::assertPushed(DispatchUploadFilesToVectorStoresForEachTenant::class);
        Queue::assertPushed(DispatchStaleCacheTagPruningForEachTenant::class);
        Queue::assertPushed(DispatchUnmatchedInboundCommunicationsForEachTenant::class);
        Queue::assertPushed(DispatchDeleteUnsavedAiThreadsForEachTenant::class);
        Queue::assertPushed(DispatchModelPruningForEachTenant::class);
        Queue::assertPushed(DispatchRefreshCalendarRefreshTokensForEachTenant::class);

        Queue::assertNotPushed(DispatchUpdateCurrentAiAssistantLinksForEachTenant::class);
        Queue::assertNotPushed(DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant::class);
    });

    it('dispatches the monthly orchestrator on the first of the month', function () {
        Queue::fake();

        travelTo(now()->startOfMonth());

        artisan('schedule:run');

        Queue::assertPushed(DispatchUpdateCurrentAiAssistantLinksForEachTenant::class);
        Queue::assertPushed(DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant::class);
    });

    it('dispatches only the per-minute tasks on an off-cadence minute', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 7));

        artisan('schedule:run');

        assertEveryMinuteTasksPushed();

        Queue::assertNotPushed(DispatchSyncCalendarsForEachTenant::class);
        Queue::assertNotPushed(DispatchUploadFilesToVectorStoresForEachTenant::class);
        Queue::assertNotPushed(DispatchStaleCacheTagPruningForEachTenant::class);
        Queue::assertNotPushed(DispatchUnmatchedInboundCommunicationsForEachTenant::class);
        Queue::assertNotPushed(DispatchDeleteUnsavedAiThreadsForEachTenant::class);
        Queue::assertNotPushed(DispatchModelPruningForEachTenant::class);
        Queue::assertNotPushed(DispatchRefreshCalendarRefreshTokensForEachTenant::class);
        Queue::assertNotPushed(DispatchUpdateCurrentAiAssistantLinksForEachTenant::class);
        Queue::assertNotPushed(DispatchUpdateCurrentCustomerAdvisorLinksForEachTenant::class);
    });

    it('dispatches the fifteen-minute orchestrators on a fifteen-minute boundary', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 15));

        artisan('schedule:run');

        assertEveryMinuteTasksPushed();

        Queue::assertPushed(DispatchSyncCalendarsForEachTenant::class);
        Queue::assertPushed(DispatchUploadFilesToVectorStoresForEachTenant::class);

        Queue::assertNotPushed(DispatchStaleCacheTagPruningForEachTenant::class);
    });

    it('dispatches the hourly orchestrator on the hour', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 0));

        artisan('schedule:run');

        Queue::assertPushed(DispatchStaleCacheTagPruningForEachTenant::class);

        Queue::assertNotPushed(DispatchModelPruningForEachTenant::class);
    });

    it('writes the schedule heartbeat to the shared store on each run', function () {
        Queue::fake();

        $check = Health::registeredChecks()->first(fn (Check $registeredCheck) => $registeredCheck instanceof ScheduleCheck);

        assert($check instanceof ScheduleCheck);

        cache()->store('health')->forget($check->getCacheKey());

        travelTo(now()->startOfDay()->setTime(10, 7));

        artisan('schedule:run');

        expect(cache()->store('health')->has($check->getCacheKey()))->toBeTrue();

        cache()->store('health')->forget($check->getCacheKey());
    });

    it('records the schedule heartbeat via the liveness beacon', function () {
        $path = storage_path('framework/schedule-heartbeat');

        @unlink($path);
        expect(file_exists($path))->toBeFalse();

        $beacon = collect(app(Kernel::class)->resolveConsoleSchedule()->events())
            ->firstWhere('description', 'Schedule Liveness Beacon');

        assert($beacon instanceof Event);

        $beacon->run(app());

        expect(file_exists($path))->toBeTrue();

        @unlink($path);
    });
});
