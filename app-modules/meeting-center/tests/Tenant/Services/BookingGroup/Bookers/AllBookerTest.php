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

it('filters available slots within lead time window', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-slots']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-slots-lead',
            'minimum_booking_lead_time_hours' => 24,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking within effective lead time window', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $member = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
    ]);

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
            'available_appointment_hours' => $workingHours,
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

it('allows booking outside effective lead time window', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar-ok']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-lead-ok',
            'minimum_booking_lead_time_hours' => 6,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking beyond effective maximum lead time', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $member = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
    ]);

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
            'available_appointment_hours' => $workingHours,
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

it('allows booking within effective maximum lead time', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar-ok']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-max-lead-ok',
            'maximum_booking_lead_time_days' => 90,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking when a member has a calendar conflict', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $member = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-calendar']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('creates calendar event on meeting owner calendar with all members as attendees', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-calendar']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
            'email' => 'owner@example.com',
        ]);

    $member = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
        'email' => 'member@example.com',
    ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($member, [], 'users')
        ->create([
            'slug' => 'test-success',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking when any one of three members has a calendar conflict', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberB = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-b-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberC = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'member-c-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($memberB, [], 'users')
        ->hasAttached($memberC, [], 'users')
        ->create([
            'slug' => 'test-three-member-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking when a prior group appointment exists at the same time', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-appt-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('rejects booking when buffer time causes a conflict with a nearby event', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-buffer-conflict',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('skips members without calendars during conflict detection', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $memberWithoutCalendar = User::factory()->create([
        'working_hours_are_enabled' => true,
        'working_hours' => $workingHours,
    ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->hasAttached($memberWithoutCalendar, [], 'users')
        ->create([
            'slug' => 'test-no-calendar-member',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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

it('allows a second booking adjacent to the first without conflict', function () use ($workingHours) {
    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC'));

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-cal']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-back-to-back',
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
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
