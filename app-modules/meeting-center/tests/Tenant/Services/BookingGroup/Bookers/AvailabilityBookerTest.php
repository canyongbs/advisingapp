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

it('returns available slots for the member with fewest meeting hours', function () use ($workingHours) {
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

    // Alice has 4 hours of meetings, Bob has 1 hour — Bob is most available
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(14),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-slots',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'avail-slots']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = $response->json('blocks');

    // Bob's availability: Apr 6 full, Apr 7 split around 10-11 busy, rest full
    expect($blocks)->toHaveCount(20);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
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
            'slug' => 'avail-no-cal',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'avail-no-cal']) . '?year=2026&month=4'
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
            'slug' => 'avail-no-cal-book',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-no-cal-book']),
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

it('books on the least busy member calendar and advances cursor', function () use ($workingHours) {
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

    // Alice has 8 hours of meetings, Bob has 2 — Bob is most available
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(8),
        'ends_at' => now()->addDays(2)->setHour(16),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(8),
        'ends_at' => now()->addDays(2)->setHour(10),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-book',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-book']),
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

    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->first();
    expect($appointment->meeting_owner_id)->toBe($bob->id);
    expect(CalendarEvent::where('calendar_id', $bob->calendar->id)->where('title', 'like', 'Group Meeting%')->exists())->toBeTrue();

    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($bob->id);
});

it('uses round robin tiebreaker when members have equal meeting hours', function () use ($workingHours) {
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

    // All three have exactly 2 hours of meetings — tied
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(10),
        'ends_at' => now()->addDays(2)->setHour(12),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(3)->setHour(14),
        'ends_at' => now()->addDays(3)->setHour(16),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $charlie->calendar->id,
        'starts_at' => now()->addDays(4)->setHour(9),
        'ends_at' => now()->addDays(4)->setHour(11),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->hasAttached($charlie, [], 'users')
        ->create([
            'slug' => 'avail-tie',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    // First booking: no cursor, goes to Alice (first alphabetically among tied)
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-tie']),
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

    // Second booking: cursor on Alice, but Alice now has 3h (2+1 from booking), Bob+Charlie have 2h
    // Bob is next among least-busy tied members (Bob, Charlie) — first alphabetically
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-tie']),
        [
            'name' => 'Visitor 2',
            'email' => 'v2@example.com',
            'starts_at' => now()->addDays(5)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(5)->setHour(11)->toIso8601String(),
        ]
    );
    $response->assertStatus(201);
    $response->assertJson(['success' => true, 'message' => 'Your appointment has been successfully booked!']);
    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($bob->id);
    $appointment2 = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->where('email', 'v2@example.com')->first();
    expect($appointment2->meeting_owner_id)->toBe($bob->id);
});

it('seamlessly books with new most-available member on conflict', function () use ($workingHours) {
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

    // Both have 0 hours, so tied — Alice resolves first (alphabetically)
    // But Alice has a conflict at the requested time
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-seamless',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    // Alice has 1h of meetings, Bob has 0h — Bob is most available
    // Alice would NOT be resolved first since Bob has fewer hours
    // But if Alice were resolved (e.g. via tiebreaker), Bob would take over seamlessly
    // With the 1h event, Bob is clearly most available, so this books with Bob directly
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-seamless']),
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

    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->first();
    expect($appointment->meeting_owner_id)->toBe($bob->id);
});

it('seamlessly rebooks with fallback member when resolved member has conflict', function () use ($workingHours) {
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

    // Both have 2 hours — tied. Alice resolves first (alphabetically, no cursor).
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(14),
        'ends_at' => now()->addDays(2)->setHour(16),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(3)->setHour(14),
        'ends_at' => now()->addDays(3)->setHour(16),
        'transparency' => 'busy',
    ]);

    // Alice also has a conflict at the exact booking time
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-fallback',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    // Alice resolves first (tied at 2h each, first alphabetically with no cursor)
    // Alice has conflict at 10-11, so re-resolve picks Bob, who has no conflict → books seamlessly
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-fallback']),
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

    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->first();
    expect($appointment->meeting_owner_id)->toBe($bob->id);
    $bookingGroup->refresh();
    expect($bookingGroup->round_robin_last_assigned_id)->toBe($bob->id);
});

it('returns 409 with fresh blocks when all members have conflicts', function () use ($workingHours) {
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

    // Both have conflicts at the requested time
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-all-conflict',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-all-conflict']),
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
    // Fresh blocks from the re-resolved member (Alice, least busy with 1h each, first alphabetically)
    // Alice's 10-11 busy on Apr 7 is carved out
    expect($blocks)->toHaveCount(20);
    expect($blocks[0])->toBe(['start' => '2026-04-06T08:00:00+00:00', 'end' => '2026-04-06T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T10:00:00+00:00']);
    expect($blocks[2])->toBe(['start' => '2026-04-07T11:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
});

it('assigns member with fewer calendar hours over member with more', function () use ($workingHours) {
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

    // Alice: 10 hours, Bob: 3 hours, Charlie: 6 hours
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(8),
        'ends_at' => now()->addDays(2)->setHour(18),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(9),
        'ends_at' => now()->addDays(2)->setHour(12),
        'transparency' => 'busy',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $charlie->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(10),
        'ends_at' => now()->addDays(2)->setHour(16),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->hasAttached($charlie, [], 'users')
        ->create([
            'slug' => 'avail-hours',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-hours']),
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
    ]);

    // Bob has fewest hours (3h) — should be assigned
    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->first();
    expect($appointment->meeting_owner_id)->toBe($bob->id);
});

it('only counts busy transparency events toward meeting hours', function () use ($workingHours) {
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

    // Alice has 4 hours of "free" events — should NOT count
    CalendarEvent::factory()->create([
        'calendar_id' => $alice->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(10),
        'ends_at' => now()->addDays(2)->setHour(14),
        'transparency' => 'free',
    ]);

    // Bob has 2 hours of "busy" events — counts
    CalendarEvent::factory()->create([
        'calendar_id' => $bob->calendar->id,
        'starts_at' => now()->addDays(2)->setHour(10),
        'ends_at' => now()->addDays(2)->setHour(12),
        'transparency' => 'busy',
    ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($alice, [], 'users')
        ->hasAttached($bob, [], 'users')
        ->create([
            'slug' => 'avail-free',
            'book_with' => BookingGroupBookWith::Availability,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'avail-free']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(201);

    // Alice has 0h busy, Bob has 2h busy — Alice is most available
    $appointment = BookingGroupAppointment::where('booking_group_id', $bookingGroup->id)->first();
    expect($appointment->meeting_owner_id)->toBe($alice->id);
});
