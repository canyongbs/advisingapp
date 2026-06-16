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

use AdvisingApp\MeetingCenter\Actions\GetAvailableGroupAppointmentSlots;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

$officeHours = [
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

it('filters available slots within lead time window', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-slots']))
        ->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-slots-lead',
            'minimum_booking_lead_time_hours' => 24,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'test-group-slots-lead']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = $response->json('blocks');

    // With 24h lead time from Monday 08:00, earliest slot is Tuesday 08:00
    // 18 weekday blocks from Apr 7 through Apr 30
    expect($blocks)->toHaveCount(18);
    expect($blocks[0])->toBe(['start' => '2026-04-07T08:00:00+00:00', 'end' => '2026-04-07T20:00:00+00:00']);
    expect($blocks[1])->toBe(['start' => '2026-04-08T08:00:00+00:00', 'end' => '2026-04-08T20:00:00+00:00']);
    // Skips weekend Apr 11-12
    expect($blocks[4])->toBe(['start' => '2026-04-13T08:00:00+00:00', 'end' => '2026-04-13T20:00:00+00:00']);
});

it('rejects booking within effective lead time window', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create();

    $member = User::factory()->create();

    // Member has 24h personal lead time
    PersonalBookingPage::factory()
        ->for($member)
        ->create(['minimum_booking_lead_time_hours' => 24]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-group-lead',
            'minimum_booking_lead_time_hours' => 6,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    // Try to book 12 hours from now (within effective 24h lead time)
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-group-lead']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addHours(12)->toIso8601String(),
            'ends_at' => now()->addHours(13)->toIso8601String(),
        ]
    );

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
    ]);
    expect($response->json('message'))->toContain('24 hours advance notice');
});

it('allows booking outside effective lead time window', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar-ok']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-lead-ok',
            'minimum_booking_lead_time_hours' => 6,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    // Book 8 hours from now (outside 6h lead time)
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-group-lead-ok']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addHours(8)->toIso8601String(),
            'ends_at' => now()->addHours(9)->toIso8601String(),
        ]
    );

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Your appointment has been successfully booked!',
        'event' => [
            'title' => 'Group Meeting with Test Visitor',
            'starts_at' => now()->addHours(8)->toIso8601String(),
            'ends_at' => now()->addHours(9)->toIso8601String(),
        ],
    ]);
});

it('rejects booking beyond effective maximum lead time', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create();

    $member = User::factory()->create();

    // Member has 60-day personal max lead time (highest wins)
    PersonalBookingPage::factory()
        ->for($member)
        ->create(['maximum_booking_lead_time_days' => 60]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-group-max-lead',
            'maximum_booking_lead_time_days' => 30,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    // Try to book 75 days from now (beyond effective 60-day max lead time)
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-group-max-lead']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(75)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(75)->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
    ]);
    expect($response->json('message'))->toContain('60 days in advance');
});

it('allows booking within effective maximum lead time', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar-ok']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-max-lead-ok',
            'maximum_booking_lead_time_days' => 90,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    // Book 45 days from now (within 90-day max lead time) - pick a weekday
    $bookingDate = now()->addDays(45);

    while ($bookingDate->isWeekend()) {
        $bookingDate->addDay();
    }

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-group-max-lead-ok']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => $bookingDate->copy()->setHour(10)->toIso8601String(),
            'ends_at' => $bookingDate->copy()->setHour(11)->toIso8601String(),
        ]
    );

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Your appointment has been successfully booked!',
        'event' => [
            'title' => 'Group Meeting with Test Visitor',
            'starts_at' => $bookingDate->copy()->setHour(10)->toIso8601String(),
            'ends_at' => $bookingDate->copy()->setHour(11)->toIso8601String(),
        ],
    ]);
});

it('rejects booking when a member has a calendar conflict', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create();

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-calendar']))
        ->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    // Create a conflicting calendar event on the member's calendar
    CalendarEvent::factory()->create([
        'calendar_id' => $member->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-conflict']),
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
        'message' => 'This time slot is no longer available. Please select another time.',
    ]);
});

it('creates calendar event on meeting owner calendar with all members as attendees', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create([
            'email' => 'owner@example.com',
        ]);

    $member = User::factory()->create([
        'email' => 'member@example.com',
    ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-success',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-success']),
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

    $calendarEvent = CalendarEvent::where('calendar_id', $meetingOwner->calendar->id)->latest()->first();
    expect($calendarEvent)->not->toBeNull();
    expect($calendarEvent->attendees)->toContain('owner@example.com');
    expect($calendarEvent->attendees)->toContain('member@example.com');
    expect($calendarEvent->attendees)->toContain('visitor@example.com');
});

it('rejects booking when any one of three members has a calendar conflict', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    $memberB = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-b-cal']))
        ->create();

    $memberC = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-c-cal']))
        ->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($memberB, [], 'users')
        ->hasAttached($memberC, [], 'users')
        ->create([
            'slug' => 'test-three-member-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $memberC->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
        'transparency' => 'busy',
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-three-member-conflict']),
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
        'message' => 'This time slot is no longer available. Please select another time.',
    ]);
});

it('rejects booking when a prior group appointment exists at the same time', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-appt-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    BookingGroupAppointment::create([
        'booking_group_id' => $bookingGroup->id,
        'name' => 'Earlier Visitor',
        'email' => 'earlier@example.com',
        'starts_at' => now()->addDays(1)->setHour(10),
        'ends_at' => now()->addDays(1)->setHour(11),
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-appt-conflict']),
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
        'message' => 'This time slot is no longer available. Please select another time.',
    ]);
});

it('rejects booking when buffer time causes a conflict with a nearby event', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-buffer-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
            'is_default_appointment_buffer_enabled' => true,
            'default_appointment_buffer_before_duration' => 30,
            'default_appointment_buffer_after_duration' => 0,
        ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $meetingOwner->calendar->id,
        'starts_at' => now()->addDays(1)->setHour(9)->setMinute(0),
        'ends_at' => now()->addDays(1)->setHour(9)->setMinute(45),
        'transparency' => 'busy',
    ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-buffer-conflict']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->setMinute(0)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->setMinute(0)->toIso8601String(),
        ]
    );

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'message' => 'This time slot is no longer available. Please select another time.',
    ]);
});

it('skips members without calendars during conflict detection', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    $memberWithoutCalendar = User::factory()->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($memberWithoutCalendar, [], 'users')
        ->create([
            'slug' => 'test-no-calendar-member',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-no-calendar-member']),
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
});

it('allows a second booking adjacent to the first without conflict', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-back-to-back',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $firstResponse = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-back-to-back']),
        [
            'name' => 'First Visitor',
            'email' => 'first@example.com',
            'starts_at' => now()->addDays(1)->setHour(10)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
        ]
    );

    $firstResponse->assertStatus(201);

    $secondResponse = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-back-to-back']),
        [
            'name' => 'Second Visitor',
            'email' => 'second@example.com',
            'starts_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(12)->toIso8601String(),
        ]
    );

    $secondResponse->assertStatus(201);
    $secondResponse->assertJson([
        'success' => true,
        'message' => 'Your appointment has been successfully booked!',
        'event' => [
            'title' => 'Group Meeting with Second Visitor',
            'starts_at' => now()->addDays(1)->setHour(11)->toIso8601String(),
            'ends_at' => now()->addDays(1)->setHour(12)->toIso8601String(),
        ],
    ]);

    expect(BookingGroupAppointment::count())->toBe(2);
});

it('excludes days when any member is out of office', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-cal']))
        ->create([
            'out_of_office_is_enabled' => true,
            'out_of_office_starts_at' => Carbon::parse('2026-04-07 00:00:00', 'UTC'),
            'out_of_office_ends_at' => Carbon::parse('2026-04-07 23:59:59', 'UTC'),
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-ooo-slots',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'test-ooo-slots']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = collect($response->json('blocks'));

    $apr7Blocks = $blocks->filter(fn (array $block) => str_contains($block['start'], '2026-04-07'));
    expect($apr7Blocks)->toBeEmpty();

    $apr8Blocks = $blocks->filter(fn (array $block) => str_contains($block['start'], '2026-04-08'));
    expect($apr8Blocks)->not->toBeEmpty();
});

it('rejects booking when a member is out of office on the requested date', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create();

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-cal']))
        ->create([
            'out_of_office_is_enabled' => true,
            'out_of_office_starts_at' => Carbon::parse('2026-04-07 00:00:00', 'UTC'),
            'out_of_office_ends_at' => Carbon::parse('2026-04-07 23:59:59', 'UTC'),
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-ooo-book',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $officeHours,
        ]);

    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-ooo-book']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => Carbon::parse('2026-04-07 10:00:00', 'UTC')->toIso8601String(),
            'ends_at' => Carbon::parse('2026-04-07 11:00:00', 'UTC')->toIso8601String(),
        ]
    );

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'message' => 'This time slot is no longer available. Please select another time.',
    ]);
});

it('does not use member personal booking availability for group booking slots', function () use ($officeHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-cal']))
        ->create([
            'office_hours_are_enabled' => true,
            'office_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '12:00'],
                'tuesday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '12:00'],
                'wednesday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '12:00'],
                'thursday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '12:00'],
                'friday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '12:00'],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ]);

    BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-ignore-office-hours',
            'available_appointment_hours' => $officeHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'test-ignore-office-hours']) . '?year=2026&month=4'
    );

    $response->assertOk();

    $blocks = collect($response->json('blocks'));

    $mondayBlock = $blocks->first(fn (array $block) => str_contains($block['start'], '2026-04-06T08:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T20:00:00+00:00');
});

$bstHours = [
    'monday' => ['is_enabled' => true, 'starts_at' => '23:00', 'ends_at' => '07:00'],
    'tuesday' => ['is_enabled' => true, 'starts_at' => '23:00', 'ends_at' => '07:00'],
    'wednesday' => ['is_enabled' => true, 'starts_at' => '23:00', 'ends_at' => '07:00'],
    'thursday' => ['is_enabled' => true, 'starts_at' => '23:00', 'ends_at' => '07:00'],
    'friday' => ['is_enabled' => true, 'starts_at' => '23:00', 'ends_at' => '07:00'],
    'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
];

$hstHours = [
    'monday' => ['is_enabled' => true, 'starts_at' => '18:00', 'ends_at' => '03:00'],
    'tuesday' => ['is_enabled' => true, 'starts_at' => '18:00', 'ends_at' => '03:00'],
    'wednesday' => ['is_enabled' => true, 'starts_at' => '18:00', 'ends_at' => '03:00'],
    'thursday' => ['is_enabled' => true, 'starts_at' => '18:00', 'ends_at' => '03:00'],
    'friday' => ['is_enabled' => true, 'starts_at' => '18:00', 'ends_at' => '03:00'],
    'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
];

$aestHours = [
    'monday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '07:00'],
    'tuesday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '07:00'],
    'wednesday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '07:00'],
    'thursday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '07:00'],
    'friday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '07:00'],
    'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
];

$nzstHours = [
    'monday' => ['is_enabled' => true, 'starts_at' => '20:00', 'ends_at' => '05:00'],
    'tuesday' => ['is_enabled' => true, 'starts_at' => '20:00', 'ends_at' => '05:00'],
    'wednesday' => ['is_enabled' => true, 'starts_at' => '20:00', 'ends_at' => '05:00'],
    'thursday' => ['is_enabled' => true, 'starts_at' => '20:00', 'ends_at' => '05:00'],
    'friday' => ['is_enabled' => true, 'starts_at' => '20:00', 'ends_at' => '05:00'],
    'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
];

it('computes slots correctly for BST positive offset crossing midnight (23:00-07:00 UTC)', function () use ($bstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-bst']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $bstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Monday Apr 6: group hours cross midnight, heuristic subDays start
    // Result should be Sun Apr 5 23:00 to Mon Apr 6 07:00 (8 hour block)
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-05T23:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T07:00:00+00:00');

    // Tuesday Apr 7: Mon Apr 6 23:00 to Tue Apr 7 07:00
    $tuesdayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-06T23:00'));
    expect($tuesdayBlock)->not->toBeNull();
    expect($tuesdayBlock['end'])->toBe('2026-04-07T07:00:00+00:00');
});

it('does not produce Saturday slots when Saturday is disabled with BST offset', function () use ($bstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-bst-sat']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $bstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    // Saturday is disabled, so no block should represent Saturday (Fri 23:00 to Sat 07:00)
    $saturdayBlocks = collect($blocks)->filter(function (array $block) {
        $end = Carbon::parse($block['end']);

        return $end->isSaturday() && $end->hour === 7;
    });

    expect($saturdayBlocks)->toBeEmpty();
});

it('computes slots correctly for HST negative offset crossing midnight (18:00-03:00 UTC)', function () use ($hstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-hst']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $hstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Monday Apr 6: heuristic addDays end
    // Result should be Mon Apr 6 18:00 to Tue Apr 7 03:00 (9 hour block)
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-06T18:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-07T03:00:00+00:00');

    // Friday Apr 10: Fri Apr 10 18:00 to Sat Apr 11 03:00
    $fridayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-10T18:00'));
    expect($fridayBlock)->not->toBeNull();
    expect($fridayBlock['end'])->toBe('2026-04-11T03:00:00+00:00');
});

it('does not produce Saturday slots when Saturday is disabled with HST offset', function () use ($hstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-hst-sat']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $hstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    // Saturday is disabled, so no block starting on Sat 18:00
    $saturdayBlocks = collect($blocks)->filter(fn ($block) => Carbon::parse($block['start'])->isSaturday() && str_contains($block['start'], 'T18:00'));

    expect($saturdayBlocks)->toBeEmpty();
});

it('computes slots correctly for AEST large positive offset crossing midnight (22:00-07:00 UTC)', function () use ($aestHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-aest']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $aestHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Monday Apr 6: heuristic subDays start (startMinFromMidnight=120, endMinFromMidnight=420)
    // Result: Sun Apr 5 22:00 to Mon Apr 6 07:00 (9 hour block)
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-05T22:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T07:00:00+00:00');
});

it('computes slots correctly for NZST very large positive offset crossing midnight (20:00-05:00 UTC)', function () use ($nzstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-nzst']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $nzstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Monday Apr 6: startMinFromMidnight=240, endMinFromMidnight=300, 240<=300 → subDay start
    // Result: Sun Apr 5 20:00 to Mon Apr 6 05:00 (9 hour block)
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-05T20:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T05:00:00+00:00');
});

it('uses only group hours without intersecting member personal hours', function () use ($bstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    // Group has wide hours (22:00-08:00 UTC) and member has narrower BST hours (23:00-07:00 UTC)
    // Group booking should only use group hours, not intersect with member hours
    $wideHours = [
        'monday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '08:00'],
        'tuesday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '08:00'],
        'wednesday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '08:00'],
        'thursday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '08:00'],
        'friday' => ['is_enabled' => true, 'starts_at' => '22:00', 'ends_at' => '08:00'],
        'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    ];

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-intersect']))
        ->create([
            'office_hours_are_enabled' => true,
            'office_hours' => $bstHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $wideHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Group hours 22:00-08:00 cross midnight, so for Monday: Sun Apr 5 22:00 to Mon Apr 6 08:00
    // Member has narrower office_hours (23:00-07:00) but they should NOT be intersected
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-05T22:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T08:00:00+00:00');
});

it('produces correct number of weekday blocks with midnight-crossing hours', function () use ($bstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-count']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $bstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    // April 2026 has 22 weekdays (Mon-Fri)
    expect($blocks)->toHaveCount(22);
});

it('uses full group hours without intersecting member personal hours for non-crossing hours', function () use ($hstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    // Group has standard non-crossing hours that encompass the full day
    $wideNonCrossingHours = [
        'monday' => ['is_enabled' => true, 'starts_at' => '00:00', 'ends_at' => '23:59'],
        'tuesday' => ['is_enabled' => true, 'starts_at' => '00:00', 'ends_at' => '23:59'],
        'wednesday' => ['is_enabled' => true, 'starts_at' => '00:00', 'ends_at' => '23:59'],
        'thursday' => ['is_enabled' => true, 'starts_at' => '00:00', 'ends_at' => '23:59'],
        'friday' => ['is_enabled' => true, 'starts_at' => '00:00', 'ends_at' => '23:59'],
        'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
    ];

    // Member has HST hours that cross midnight (18:00-03:00 UTC)
    // But group booking should NOT intersect with member personal hours
    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-hst-member']))
        ->create([
            'office_hours_are_enabled' => true,
            'office_hours' => $hstHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $wideNonCrossingHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    expect($blocks)->not->toBeEmpty();

    // Group hours 00:00-23:59 are used directly — member's narrower HST hours are NOT intersected
    $mondayBlock = collect($blocks)->first(fn ($block) => str_contains($block['start'], '2026-04-06T00:00'));
    expect($mondayBlock)->not->toBeNull();
    expect($mondayBlock['end'])->toBe('2026-04-06T23:59:00+00:00');
});

it('does not produce slots outside the month boundary with midnight-crossing hours', function () use ($bstHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-01 00:00:00', 'UTC'));

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'cal-boundary']))
        ->create();

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($member, [], 'users')
        ->create([
            'available_appointment_hours' => $bstHours,
        ]);

    $action = new GetAvailableGroupAppointmentSlots();
    $blocks = $action($bookingGroup, 2026, 4);

    foreach ($blocks as $block) {
        $start = Carbon::parse($block['start']);
        $end = Carbon::parse($block['end']);

        // Start can be Mar 31 23:00 at earliest (for Wed Apr 1's midnight-crossing slot)
        expect($start->gte(Carbon::parse('2026-03-31T23:00:00+00:00')))->toBeTrue();
        // End should not go past Apr 30
        expect($end->lte(Carbon::parse('2026-05-01T07:00:00+00:00')))->toBeTrue();
    }
});
