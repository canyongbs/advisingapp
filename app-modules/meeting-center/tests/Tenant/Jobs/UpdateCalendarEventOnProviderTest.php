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
use AdvisingApp\MeetingCenter\Jobs\Middleware\CalendarRequestsConcurrencyLimit;
use AdvisingApp\MeetingCenter\Jobs\UpdateCalendarEventOnProvider;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Queue::fake();
});

function makeUpdateJobCalendarEvent(): CalendarEvent
{
    $calendar = Calendar::factory()
        ->for(User::factory())
        ->create([
            'provider_id' => 'job-calendar',
        ]);

    return CalendarEvent::factory()
        ->for($calendar)
        ->createQuietly();
}

it('updates the event on the resolved provider driver when already synced', function () {
    $event = makeUpdateJobCalendarEvent();
    $event->updateQuietly(['provider_id' => 'already-synced']);

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('updateEvent') // @phpstan-ignore method.notFound
        ->once()
        ->with(Mockery::on(fn (CalendarEvent $argument): bool => $argument->is($event)));

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->with('google')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    (new UpdateCalendarEventOnProvider($event))->handle();
});

it('falls back to creating the event when it was never synced', function () {
    $event = makeUpdateJobCalendarEvent();

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('createEvent') // @phpstan-ignore method.notFound
        ->once()
        ->with(Mockery::on(fn (CalendarEvent $argument): bool => $argument->is($event)));
    $driver->shouldNotReceive('updateEvent'); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    (new UpdateCalendarEventOnProvider($event))->handle();
});

it('swallows a CouldNotRefreshToken failure', function () {
    $event = makeUpdateJobCalendarEvent();
    $event->updateQuietly(['provider_id' => 'already-synced']);

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('updateEvent')->once()->andThrow(new CouldNotRefreshToken()); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    (new UpdateCalendarEventOnProvider($event))->handle();
});

it('releases the job when the provider is rate limited', function () {
    $event = makeUpdateJobCalendarEvent();
    $event->updateQuietly(['provider_id' => 'already-synced']);

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('updateEvent')->once()->andThrow(new MicrosoftGraphRateLimited(retryAfterSeconds: 45)); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    $job = (new UpdateCalendarEventOnProvider($event))->withFakeQueueInteractions();

    $job->handle();
    $job->assertReleased(45);
});

it('rethrows MicrosoftGraphRateLimited without retryAfterSeconds', function () {
    $event = makeUpdateJobCalendarEvent();
    $event->updateQuietly(['provider_id' => 'already-synced']);

    $driver = Mockery::mock(CalendarInterface::class);
    $driver->shouldReceive('updateEvent')->once()->andThrow(new MicrosoftGraphRateLimited()); // @phpstan-ignore method.notFound

    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);

    expect(fn () => (new UpdateCalendarEventOnProvider($event))->handle())
        ->toThrow(MicrosoftGraphRateLimited::class);
});

it('prevents overlaps with other provider jobs for the same event', function () {
    $event = makeUpdateJobCalendarEvent();

    $middleware = (new UpdateCalendarEventOnProvider($event))->middleware();

    expect($middleware)->toHaveCount(2);
    expect($middleware[0])->toBeInstanceOf(WithoutOverlapping::class);
    expect($middleware[1])->toBeInstanceOf(CalendarRequestsConcurrencyLimit::class);

    $overlapKey = (new ReflectionProperty($middleware[0], 'key'))->getValue($middleware[0]);
    $shareKey = (new ReflectionProperty($middleware[0], 'shareKey'))->getValue($middleware[0]);

    expect((string) $overlapKey)->toBe($event->id);
    expect((bool) $shareKey)->toBeTrue();
});

it('has maxExceptions of 3', function () {
    $event = makeUpdateJobCalendarEvent();

    expect((new UpdateCalendarEventOnProvider($event))->maxExceptions)->toBe(3);
});

it('uses a backoff of 10 seconds', function () {
    $event = makeUpdateJobCalendarEvent();

    expect((new UpdateCalendarEventOnProvider($event))->backoff())->toBe(10);
});

it('retries for an hour', function () {
    $event = makeUpdateJobCalendarEvent();

    expect((new UpdateCalendarEventOnProvider($event))->retryUntil()->format(DATE_ATOM))
        ->toBe(now()->addHour()->format(DATE_ATOM));
});
