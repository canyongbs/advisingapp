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

use AdvisingApp\MeetingCenter\Enums\CalendarProvider;
use AdvisingApp\MeetingCenter\Exceptions\CouldNotRefreshToken;
use AdvisingApp\MeetingCenter\Exceptions\MicrosoftGraphRateLimited;
use AdvisingApp\MeetingCenter\Jobs\Middleware\CalendarRequestsConcurrencyLimit;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendarPeriod;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Queue\Job;

/** @param array<string, mixed> $overrides */
function createSyncCalendar(array $overrides = []): Calendar
{
    return Calendar::factory()
        ->for(User::factory())
        ->create(array_merge([
            'provider_type' => CalendarProvider::Outlook,
            'provider_id' => 'test-calendar-id',
            'provider_email' => 'test@example.com',
            'oauth_token' => 'test-token',
            'oauth_refresh_token' => 'test-refresh-token',
            'oauth_token_expires_at' => now()->addHour(),
        ], $overrides));
}

// ──────────────────────────────────────────────────
// Core functionality
// ──────────────────────────────────────────────────

it('calls syncEvents on the correct driver with correct dates', function () {
    $calendar = createSyncCalendar();
    $start = now()->startOfMonth();
    $end = now()->endOfMonth();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('syncEvents') // @phpstan-ignore method.notFound
        ->once()
        ->withArgs(function (Calendar $cal, DateTime $startArg, DateTime $endArg) use ($calendar, $start, $end) {
            return $cal->is($calendar)
                && $startArg->format('Y-m-d H:i:s') === $start->toDateTimeString()
                && $endArg->format('Y-m-d H:i:s') === $end->toDateTimeString();
        });

    $calendarManager = Mockery::mock(CalendarManager::class);
    $calendarManager->shouldReceive('driver') // @phpstan-ignore method.notFound
        ->with('outlook')
        ->andReturn($driver);

    app()->instance(CalendarManager::class, $calendarManager);

    $job = new SyncCalendarPeriod($calendar, $start, $end);
    $job->handle();
});

// ──────────────────────────────────────────────────
// Error handling
// ──────────────────────────────────────────────────

it('fails permanently on CouldNotRefreshToken', function () {
    $calendar = createSyncCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('syncEvents') // @phpstan-ignore method.notFound
        ->andThrow(new CouldNotRefreshToken('Token expired'));

    $calendarManager = Mockery::mock(CalendarManager::class);
    $calendarManager->shouldReceive('driver') // @phpstan-ignore method.notFound
        ->with('outlook')
        ->andReturn($driver);

    app()->instance(CalendarManager::class, $calendarManager);

    $job = new SyncCalendarPeriod($calendar, now()->startOfMonth(), now()->endOfMonth());

    $fakeQueueJob = Mockery::mock(Job::class);
    $fakeQueueJob->shouldReceive('fail') // @phpstan-ignore method.notFound
        ->once()
        ->withArgs(fn ($exception) => $exception instanceof CouldNotRefreshToken);
    $job->job = $fakeQueueJob; // @phpstan-ignore assign.propertyType

    $job->handle();
});

it('releases the job with retryAfterSeconds on MicrosoftGraphRateLimited', function () {
    $calendar = createSyncCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('syncEvents') // @phpstan-ignore method.notFound
        ->andThrow(new MicrosoftGraphRateLimited(retryAfterSeconds: 45));

    $calendarManager = Mockery::mock(CalendarManager::class);
    $calendarManager->shouldReceive('driver') // @phpstan-ignore method.notFound
        ->with('outlook')
        ->andReturn($driver);

    app()->instance(CalendarManager::class, $calendarManager);

    $job = new SyncCalendarPeriod($calendar, now()->startOfMonth(), now()->endOfMonth());

    $fakeQueueJob = Mockery::mock(Job::class);
    $fakeQueueJob->shouldReceive('release') // @phpstan-ignore method.notFound
        ->once()
        ->with(45);
    $job->job = $fakeQueueJob; // @phpstan-ignore assign.propertyType

    expect(fn () => $job->handle())
        ->toThrow(MicrosoftGraphRateLimited::class);
});

it('rethrows MicrosoftGraphRateLimited after releasing', function () {
    $calendar = createSyncCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('syncEvents') // @phpstan-ignore method.notFound
        ->andThrow(new MicrosoftGraphRateLimited(retryAfterSeconds: 30));

    $calendarManager = Mockery::mock(CalendarManager::class);
    $calendarManager->shouldReceive('driver') // @phpstan-ignore method.notFound
        ->with('outlook')
        ->andReturn($driver);

    app()->instance(CalendarManager::class, $calendarManager);

    $job = new SyncCalendarPeriod($calendar, now()->startOfMonth(), now()->endOfMonth());

    $fakeQueueJob = Mockery::mock(Job::class);
    $fakeQueueJob->shouldReceive('release')->once(); // @phpstan-ignore method.notFound
    $job->job = $fakeQueueJob; // @phpstan-ignore assign.propertyType

    expect(fn () => $job->handle())
        ->toThrow(MicrosoftGraphRateLimited::class);
});

// ──────────────────────────────────────────────────
// Job configuration
// ──────────────────────────────────────────────────

it('has correct uniqueId format', function () {
    $calendar = createSyncCalendar();
    $start = now()->startOfMonth();
    $end = now()->endOfMonth();

    $job = new SyncCalendarPeriod($calendar, $start, $end);

    $expectedId = Tenant::current()->getKey() . ':' . $calendar->getKey() . ':' . $start->format('Y-m-d') . ':' . $end->format('Y-m-d');

    expect($job->uniqueId())->toBe($expectedId);
});

it('uses CalendarRequestsConcurrencyLimit middleware', function () {
    $calendar = createSyncCalendar();

    $job = new SyncCalendarPeriod($calendar, now()->startOfMonth(), now()->endOfMonth());

    $middleware = $job->middleware();

    expect($middleware)->toHaveCount(1)
        ->and($middleware[0])->toBeInstanceOf(CalendarRequestsConcurrencyLimit::class);
});

it('has maxExceptions of 3', function () {
    $calendar = createSyncCalendar();

    $job = new SyncCalendarPeriod($calendar, now()->startOfMonth(), now()->endOfMonth());

    expect($job->maxExceptions)->toBe(3);
});
