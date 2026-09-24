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

use AdvisingApp\MeetingCenter\Exceptions\CouldNotRefreshToken;
use AdvisingApp\MeetingCenter\Exceptions\MicrosoftGraphRateLimited;
use AdvisingApp\MeetingCenter\Jobs\DeleteCalendarEventFromProvider;
use AdvisingApp\MeetingCenter\Jobs\Middleware\CalendarRequestsConcurrencyLimit;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

use function Pest\Laravel\travelTo;

beforeEach(function (): void {
    Queue::fake();
});

function makeDeleteJobCalendar(): Calendar
{
    return Calendar::factory()
        ->for(User::factory())
        ->create(['provider_id' => 'job-calendar']);
}

it('deletes the event on the resolved provider driver', function () {
    $calendar = makeDeleteJobCalendar();
    $eventId = (string) Str::uuid();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('deleteEvent') // @phpstan-ignore method.notFound
        ->once()
        ->with(Mockery::on(function (CalendarEvent $argument) use ($calendar): bool {
            return $argument->provider_id === 'provider-event-id'
                && $argument->calendar->is($calendar);
        }));

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->with('google')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    (new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', $eventId))->handle();
});

it('swallows a CouldNotRefreshToken failure', function () {
    $calendar = makeDeleteJobCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('deleteEvent')->once()->andThrow(new CouldNotRefreshToken()); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    (new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->handle();
});

it('releases the job when the provider is rate limited', function () {
    $calendar = makeDeleteJobCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('deleteEvent')->once()->andThrow(new MicrosoftGraphRateLimited(retryAfterSeconds: 45)); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    $job = (new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->withFakeQueueInteractions();

    $job->handle();
    $job->assertReleased(45);
});

it('rethrows MicrosoftGraphRateLimited without retryAfterSeconds', function () {
    $calendar = makeDeleteJobCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('deleteEvent')->once()->andThrow(new MicrosoftGraphRateLimited()); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    expect(fn () => (new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->handle())
        ->toThrow(MicrosoftGraphRateLimited::class);
});

it('prevents overlaps with other provider jobs for the same event', function () {
    $calendar = makeDeleteJobCalendar();
    $eventId = (string) Str::uuid();

    $middleware = (new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', $eventId))->middleware();

    expect($middleware)->toHaveCount(2);
    expect($middleware[0])->toBeInstanceOf(WithoutOverlapping::class);
    expect($middleware[1])->toBeInstanceOf(CalendarRequestsConcurrencyLimit::class);

    $overlapKey = (new ReflectionProperty($middleware[0], 'key'))->getValue($middleware[0]);
    $shareKey = (new ReflectionProperty($middleware[0], 'shareKey'))->getValue($middleware[0]);

    expect((string) $overlapKey)->toBe($eventId);
    expect((bool) $shareKey)->toBeTrue();
    expect((int) $middleware[0]->releaseAfter)->toBe(10);
    expect((int) $middleware[0]->expiresAfter)->toBe(60);
});

it('has maxExceptions of 3', function () {
    $calendar = makeDeleteJobCalendar();

    expect((new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->maxExceptions)->toBe(3);
});

it('is placed on configured queue', function () {
    $calendar = makeDeleteJobCalendar();

    $job = new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid());

    expect($job->queue)->toBe(config('meeting-center.queue'));
});

it('uses a backoff of 10 seconds', function () {
    $calendar = makeDeleteJobCalendar();

    expect((new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->backoff())->toBe(10);
});

it('retries for an hour', function () {
    $calendar = makeDeleteJobCalendar();

    travelTo(Carbon::parse('2026-09-23 12:00:00'));

    expect((new DeleteCalendarEventFromProvider($calendar, 'provider-event-id', (string) Str::uuid()))->retryUntil()->format(DATE_ATOM))
        ->toBe(now()->addHour()->format(DATE_ATOM));
});
