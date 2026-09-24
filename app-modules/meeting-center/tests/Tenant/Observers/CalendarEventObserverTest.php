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

use AdvisingApp\MeetingCenter\Jobs\DeleteCalendarEventFromProvider;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendarEventToProvider;
use AdvisingApp\MeetingCenter\Jobs\UpdateCalendarEventOnProvider;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

function makeObserverCalendar(): Calendar
{
    return Calendar::factory()
        ->for(User::factory())
        ->create(['provider_id' => 'observer-calendar']);
}

it('queues an after-commit provider sync when a calendar event is created', function () {
    $calendar = makeObserverCalendar();

    $driverCalledAfterCommit = null;
    $driver = Mockery::mock(CalendarInterface::class);
    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);
    $driver->shouldReceive('createEvent') // @phpstan-ignore method.notFound
        ->once()
        ->andReturnUsing(function () use (&$driverCalledAfterCommit): void {
            $driverCalledAfterCommit = true;
        });

    DB::beginTransaction();

    CalendarEvent::factory()->create(['calendar_id' => $calendar->id]);

    expect($driverCalledAfterCommit)->toBeNull();

    DB::commit();

    expect($driverCalledAfterCommit)->toBeTrue();
});

it('does not queue a provider sync when a calendar event is created quietly', function () {
    $calendar = makeObserverCalendar();

    Queue::fake();

    CalendarEvent::factory()->make(['calendar_id' => $calendar->id])->saveQuietly();

    Queue::assertNotPushed(SyncCalendarEventToProvider::class);
});

it('does not push to the provider when the surrounding transaction rolls back', function () {
    $calendar = makeObserverCalendar();

    $driver = Mockery::mock(CalendarInterface::class);
    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);
    $driver->shouldNotReceive('createEvent'); // @phpstan-ignore method.notFound

    rescue(function () use ($calendar): void {
        DB::transaction(function () use ($calendar): void {
            CalendarEvent::factory()->create(['calendar_id' => $calendar->id]);

            throw new RuntimeException('Booking failed after the event was created.');
        });
    }, report: false);

    expect(CalendarEvent::query()->count())->toBe(0);
});

it('queues an after-commit provider update when a calendar event is updated', function () {
    $calendar = makeObserverCalendar();
    $event = CalendarEvent::factory()->createQuietly([
        'calendar_id' => $calendar->id,
        'provider_id' => 'synced-event',
    ]);

    $driverCalledAfterCommit = null;
    $driver = Mockery::mock(CalendarInterface::class);
    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);
    $driver->shouldReceive('updateEvent') // @phpstan-ignore method.notFound
        ->once()
        ->andReturnUsing(function () use (&$driverCalledAfterCommit): void {
            $driverCalledAfterCommit = true;
        });

    DB::beginTransaction();

    $event->update(['title' => 'Updated title']);

    expect($driverCalledAfterCommit)->toBeNull();

    DB::commit();

    expect($driverCalledAfterCommit)->toBeTrue();
});

it('does not queue a provider update when a calendar event is updated quietly', function () {
    $calendar = makeObserverCalendar();

    Queue::fake();

    $event = CalendarEvent::factory()->createQuietly(['calendar_id' => $calendar->id]);

    $event->updateQuietly(['title' => 'Updated title']);

    Queue::assertNotPushed(UpdateCalendarEventOnProvider::class);
});

it('queues an after-commit provider delete when a synced calendar event is deleted', function () {
    $calendar = makeObserverCalendar();
    $event = CalendarEvent::factory()->createQuietly([
        'calendar_id' => $calendar->id,
        'provider_id' => 'synced-event',
    ]);

    $driverCalledAfterCommit = null;
    $driver = Mockery::mock(CalendarInterface::class);
    $manager = Mockery::mock(CalendarManager::class);
    $manager->shouldReceive('driver')->andReturn($driver); // @phpstan-ignore method.notFound
    app()->instance(CalendarManager::class, $manager);
    $driver->shouldReceive('deleteEvent') // @phpstan-ignore method.notFound
        ->once()
        ->andReturnUsing(function () use (&$driverCalledAfterCommit): void {
            $driverCalledAfterCommit = true;
        });

    DB::beginTransaction();

    $event->delete();

    expect($driverCalledAfterCommit)->toBeNull();

    DB::commit();

    expect($driverCalledAfterCommit)->toBeTrue();
});

it('does not queue a provider delete when the event was never synced', function () {
    $calendar = makeObserverCalendar();

    Queue::fake();

    $event = CalendarEvent::factory()->createQuietly(['calendar_id' => $calendar->id]);

    $event->delete();

    Queue::assertNotPushed(DeleteCalendarEventFromProvider::class);
});

it('does not queue a provider delete when a calendar event is deleted quietly', function () {
    $calendar = makeObserverCalendar();

    Queue::fake();

    $event = CalendarEvent::factory()->createQuietly([
        'calendar_id' => $calendar->id,
        'provider_id' => 'synced-event',
    ]);

    $event->deleteQuietly();

    Queue::assertNotPushed(DeleteCalendarEventFromProvider::class);
});
