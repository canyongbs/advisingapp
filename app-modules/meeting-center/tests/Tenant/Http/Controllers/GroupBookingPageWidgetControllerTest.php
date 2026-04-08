<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Features\MinimumLeadTimeFeature;
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

// Minimum Lead Time - Available Slots Tests

it('filters group available slots within lead time window', function () use ($workingHours) {
    MinimumLeadTimeFeature::activate();

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

    /** @var array<int, array{start: string, end: string}> $blocksData */
    $blocksData = $response->json('blocks');
    $blocks = collect($blocksData);

    // With 24h lead time from Monday 08:00, earliest slot should be Tuesday 08:00+
    $blocksBeforeLeadTime = $blocks->filter(function (array $block) {
        return Carbon::parse($block['start'])->isBefore(Carbon::parse('2026-04-07 08:00:00', 'UTC'));
    });

    expect($blocksBeforeLeadTime)->toBeEmpty();
});

// TODO: FeatureFlag Cleanup - This test can be removed when MinimumLeadTimeFeature is removed
it('returns group available slots within lead time window when feature is inactive', function () use ($workingHours) {
    MinimumLeadTimeFeature::deactivate();

    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-slots-no-flag']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-slots-no-flag',
            'minimum_booking_lead_time_hours' => 24,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
        ]);

    $response = getJson(
        route('widgets.booking-page.group.api.available-slots', ['slug' => 'test-group-slots-no-flag']) . '?year=2026&month=4'
    );

    $response->assertOk();

    /** @var array<int, array{start: string, end: string}> $blocksData */
    $blocksData = $response->json('blocks');
    $blocks = collect($blocksData);

    // Feature is off, so Monday slots should still be available
    $mondayBlocks = $blocks->filter(function (array $block) {
        return Carbon::parse($block['start'])->day === 6;
    });

    expect($mondayBlocks)->not->toBeEmpty();
});

// Minimum Lead Time - Booking Validation Tests

it('rejects group booking within effective lead time window', function () use ($workingHours) {
    MinimumLeadTimeFeature::activate();

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
    $response->assertJsonFragment(['success' => false]);
    expect($response->json('message'))->toContain('24 hours advance notice');
});

it('allows group booking outside effective lead time window', function () use ($workingHours) {
    MinimumLeadTimeFeature::activate();

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
    $response->assertJsonFragment(['success' => true]);
});

// TODO: FeatureFlag Cleanup - This test can be removed when MinimumLeadTimeFeature is removed
it('ignores lead time for group booking when feature is inactive', function () use ($workingHours) {
    MinimumLeadTimeFeature::deactivate();

    Carbon::setTestNow(Carbon::parse('2026-04-06 08:00:00', 'UTC')); // Monday

    $meetingOwner = User::factory()
        ->has(Calendar::factory()->state(['provider_id' => 'owner-no-flag']))
        ->create([
            'working_hours_are_enabled' => true,
            'working_hours' => $workingHours,
        ]);

    $bookingGroup = BookingGroup::factory()
        ->hasAttached($meetingOwner, [], 'users')
        ->create([
            'slug' => 'test-group-no-flag',
            'minimum_booking_lead_time_hours' => 24,
            'meeting_owner_id' => $meetingOwner->id,
            'available_appointment_hours' => $workingHours,
        ]);

    // Book 2 hours from now - within 24h lead time, but flag is off
    $response = postJson(
        route('widgets.booking-page.group.api.book', ['slug' => 'test-group-no-flag']),
        [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => now()->addHours(2)->toIso8601String(),
            'ends_at' => now()->addHours(3)->toIso8601String(),
        ]
    );

    $response->assertStatus(201);
    $response->assertJsonFragment(['success' => true]);
});
