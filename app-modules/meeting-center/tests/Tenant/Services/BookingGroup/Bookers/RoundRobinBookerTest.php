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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\MeetingCenter\Enums\BookingGroupBookWith;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

$workingHours = [
    'monday' => ['is_enabled' => true, 'starts_at' => '08:00', 'ends_at' => '20:00'],
    'tuesday' => ['is_enabled' => true, 'starts_at' => '08:00', 'ends_at' => '20:00'],
    'wednesday' => ['is_enabled' => true, 'starts_at' => '08:00', 'ends_at' => '20:00'],
    'thursday' => ['is_enabled' => true, 'starts_at' => '08:00', 'ends_at' => '20:00'],
    'friday' => ['is_enabled' => true, 'starts_at' => '08:00', 'ends_at' => '20:00'],
    'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
];

beforeEach(function () {
    $mockDriver = Mockery::mock(CalendarInterface::class);
    /** @phpstan-ignore method.notFound */
    $mockDriver->shouldReceive('createEvent')->andReturnUsing(function (CalendarEvent $event) {
        $event->updateQuietly([
            'provider_id' => 'mock-provider-id',
            'provider_uid' => 'mock-provider-uid',
        ]);
    });
    $mockDriver->shouldReceive('updateEvent')->andReturn(null);
    $mockDriver->shouldReceive('deleteEvent')->andReturn(null);

    $mockManager = Mockery::mock(CalendarManager::class, function (MockInterface $mock) use ($mockDriver) {
        $mock->shouldReceive('driver')->andReturn($mockDriver);
    });

    app()->instance(CalendarManager::class, $mockManager);
});

it('returns available slots for the current round robin member', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $memberA = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-a']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberB = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-b']))
        ->create([
            'name' => 'Bob',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($memberA, [], 'users')
        ->hasAttached($memberB, [], 'users')
        ->create([
            'slug' => 'rr-slots',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'rr-slots']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = $response->json('blocks');

    // 19 weekday blocks for Alice (Apr 6 Mon through Apr 30 Thu)
    expect($blocks)->toHaveCount(19);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
});

it('returns empty slots when no members have connected calendars', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $member = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
    ]);

    BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'rr-no-cal',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'rr-no-cal']) . '?year=2026&month=4'
    );

    $response->assertOk();
    $response->assertJson(['blocks' => []]);
});

it('returns 422 when no members have connected calendars at booking time', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $member = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
    ]);

    BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'rr-no-cal-book',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-no-cal-book']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'No available members with connected calendars.',
    ]);
});

it('creates event on the round robin member calendar and advances cursor', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $memberA = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-a']))
        ->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberB = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-b']))
        ->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($memberA, [], 'users')
        ->hasAttached($memberB, [], 'users')
        ->create([
            'slug' => 'rr-book-ok',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-book-ok']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Your appointment has been successfully booked!',
        'event' => [
            'title' => 'Group Meeting with Test Visitor',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ],
    ]);

    $calendarEvent = CalendarEvent::where('calendar_id', $memberA->calendar->id)->latest()->first();
    expect($calendarEvent)->not->toBeNull();
    expect($calendarEvent->attendees)->toContain('alice@example.com');
    expect($calendarEvent->attendees)->toContain('visitor@example.com');

    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->latest()->first();
    expect($appointment->meeting_owner_id)->toBe($memberA->id);

    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($memberA->id);
});

it('returns 409 with fresh blocks when round robin member has a conflict', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $memberA = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-a']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberB = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-b']))
        ->create([
            'name' => 'Bob',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($memberA, [], 'users')
        ->hasAttached($memberB, [], 'users')
        ->create([
            'slug' => 'rr-conflict',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $memberA->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-conflict']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'message' => 'This time slot is no longer available.',
    ]);

    $blocks = $response->json('blocks');

    // Fresh blocks for Alice with her 10-11 busy event carved out on Apr 7
    expect($blocks)->toHaveCount(20);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
    expect($blocks[3])->toBe(['start' => '2026-04-08T08:00:00+00:00', 'end' => '2026-04-08T20:00:00+00:00']);
});

it('cycles cursor through three members in alphabetical order', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $alice = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-alice']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bob = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-bob']))
        ->create([
            'name' => 'Bob',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $charlie = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-charlie']))
        ->create([
            'name' => 'Charlie',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->hasAttached($charlie, [], 'users')
        ->create([
            'slug' => 'rr-cycle',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-cycle']),
        [
            'name' => 'Visitor 1',
            'email' => 'v1@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );
    $response->assertStatus(201);
    $response->assertJson(['success' => true, 'message' => 'Your appointment has been successfully booked!']);
    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($alice->id);
    $appointment1 = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->where('email', 'v1@example.com')->first();
    expect($appointment1->meeting_owner_id)->toBe($alice->id);
    expect(CalendarEvent::where('calendar_id', $alice->calendar->id)->exists())->toBeTrue();

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-cycle']),
        [
            'name' => 'Visitor 2',
            'email' => 'v2@example.com',
            'starts_at' => now()->addDays(2)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(2)->setHour(11)->toIso8601String(),
        ]
    );
    $response->assertStatus(201);
    $response->assertJson(['success' => true, 'message' => 'Your appointment has been successfully booked!']);
    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($bob->id);
    $appointment2 = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->where('email', 'v2@example.com')->first();
    expect($appointment2->meeting_owner_id)->toBe($bob->id);
    expect(CalendarEvent::where('calendar_id', $bob->calendar->id)->exists())->toBeTrue();

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-cycle']),
        [
            'name' => 'Visitor 3',
            'email' => 'v3@example.com',
            'starts_at' => now()->addDays(3)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(3)->setHour(11)->toIso8601String(),
        ]
    );
    $response->assertStatus(201);
    $response->assertJson(['success' => true, 'message' => 'Your appointment has been successfully booked!']);
    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($charlie->id);
    $appointment3 = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->where('email', 'v3@example.com')->first();
    expect($appointment3->meeting_owner_id)->toBe($charlie->id);
    expect(CalendarEvent::where('calendar_id', $charlie->calendar->id)->exists())->toBeTrue();

    $slotsResponse = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'rr-cycle']) . '?year=2026&month=4'
    );
    $slotsResponse->assertOk();

    $blocks = $slotsResponse->json('blocks');

    // Alice is next (cursor wraps), her Apr 7 10-11 appointment is carved out
    expect($blocks)->toHaveCount(20);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);

    expect(BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->count())->toBe(3);
});

it('rejects booking when a prior group appointment for the member conflicts', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-member']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'rr-appt-conflict',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    BookingGroupAppointment::create([
        'booking_group_id' => $bookingGroup->id,
        'meeting_owner_id' => $member->id,
        'name' => 'Earlier Visitor',
        'email' => 'earlier@example.com',
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-appt-conflict']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'message' => 'This time slot is no longer available.',
    ]);

    $blocks = $response->json('blocks');

    // Fresh blocks for Alice with her existing appointment carved out
    expect($blocks)->toHaveCount(20);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
});

it('returns fresh blocks from the next member on conflict', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $alice = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-alice']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bob = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-bob']))
        ->create([
            'name' => 'Bob',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $charlie = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-charlie']))
        ->create([
            'name' => 'Charlie',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->hasAttached($charlie, [], 'users')
        ->create([
            'slug' => 'rr-next-member',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'rr-next-member']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'message' => 'This time slot is no longer available.',
    ]);

    $blocks = $response->json('blocks');

    // Fresh blocks for Alice with her busy event carved out on Apr 7
    expect($blocks)->toHaveCount(20);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
});

it('only shows availability for the assigned member, not other members', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $alice = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-alice']))
        ->create([
            'name' => 'Alice',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bob = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-bob']))
        ->create([
            'name' => 'Bob',
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'rr-isolated-availability',
            'book_with' => BookingGroupBookWith::RoundRobin,
            'available_appointment_hours' => $workingHours,
        ]);

    for ($hour = 8; $hour < 18; $hour++) {
        CalendarEvent::factory()->create([
            'calendar_id' => $bob->calendar->id,
            'starts_at' => Carbon::parse('2026-04-07')->setHour($hour),
            'ends_at' => Carbon::parse('2026-04-07')->setHour($hour + 1),
            'transparency' => 'busy',
        ]);
    }

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'rr-isolated-availability']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = $response->json('blocks');

    // Alice is assigned (first alphabetically), Bob's busy events are irrelevant
    expect($blocks)->toHaveCount(19);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    // Apr 7 is a full block because Alice has no conflicts
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
});
