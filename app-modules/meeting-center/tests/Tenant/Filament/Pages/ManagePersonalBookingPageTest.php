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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Filament\Pages\ManagePersonalBookingPage;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can render the page', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    actingAs($user);

    get(ManagePersonalBookingPage::getUrl())
        ->assertSuccessful();
});

it('creates a personal booking page when enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-user-booking',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount(PersonalBookingPage::class, 1);

    assertDatabaseHas(PersonalBookingPage::class, [
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'test-user-booking',
        'default_appointment_duration' => 30,
    ]);

    expect($user->fresh()->working_hours_are_enabled)->toBeTrue();
});

it('validates slug is required when enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => '',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['slug' => 'required']);
});

it('validates slug must be unique', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();
    $otherUser = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    PersonalBookingPage::factory()->create([
        'user_id' => $otherUser->id,
        'slug' => 'existing-slug',
    ]);

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'existing-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('validates slug must be alpha dash', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'invalid slug with spaces',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['slug' => 'alpha_dash']);
});

it('validates slug max length', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => str_repeat('a', 256),
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['slug' => 'max']);
});

it('validates default appointment duration is required when enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => null,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['default_appointment_duration' => 'required']);
});

it('validates working hours are required when booking page is enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [],
        ])
        ->call('save')
        ->assertHasFormErrors(['working_hours_are_enabled']);
});

it('validates at least one day must have working hours configured', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['working_hours_are_enabled']);
});

it('can update an existing personal booking page', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    $bookingPage = PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'original-slug',
        'default_appointment_duration' => 30,
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'updated-slug',
            'default_appointment_duration' => 60,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $bookingPage->refresh();

    expect($bookingPage->slug)->toBe('updated-slug');
    expect($bookingPage->default_appointment_duration)->toBe(60);
});

it('can disable a personal booking page', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    $bookingPage = PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'test-slug',
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => false,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $bookingPage->refresh();

    expect($bookingPage->is_enabled)->toBeFalse();
});

it('saves working hours configuration to user', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    $formWorkingHours = [
        'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
        'tuesday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '18:00'],
        'wednesday' => ['is_enabled' => false],
        'thursday' => ['is_enabled' => false],
        'friday' => ['is_enabled' => false],
        'saturday' => ['is_enabled' => false],
        'sunday' => ['is_enabled' => false],
    ];

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'are_working_hours_visible_on_profile' => true,
            'working_hours' => $formWorkingHours,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->working_hours_are_enabled)->toBeTrue();
    expect($user->are_working_hours_visible_on_profile)->toBeTrue();
    expect($user->working_hours['monday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['tuesday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['wednesday']['is_enabled'] ?? false)->toBeFalse();
    expect($user->working_hours['thursday']['is_enabled'] ?? false)->toBeFalse();
});

it('saves office hours configuration to user', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    $formOfficeHours = [
        'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
        'tuesday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '18:00'],
        'wednesday' => ['is_enabled' => false],
        'thursday' => ['is_enabled' => false],
        'friday' => ['is_enabled' => false],
        'saturday' => ['is_enabled' => false],
        'sunday' => ['is_enabled' => false],
    ];

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false],
                'wednesday' => ['is_enabled' => false],
                'thursday' => ['is_enabled' => false],
                'friday' => ['is_enabled' => false],
                'saturday' => ['is_enabled' => false],
                'sunday' => ['is_enabled' => false],
            ],
            'office_hours_are_enabled' => true,
            'office_hours' => $formOfficeHours,
            'appointments_are_restricted_to_existing_students' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->office_hours_are_enabled)->toBeTrue();
    expect($user->appointments_are_restricted_to_existing_students)->toBeTrue();
    expect($user->office_hours['monday']['is_enabled'])->toBeTrue();
    expect($user->office_hours['tuesday']['is_enabled'])->toBeTrue();
    expect($user->office_hours['wednesday']['is_enabled'] ?? false)->toBeFalse();
    expect($user->office_hours['thursday']['is_enabled'] ?? false)->toBeFalse();
});

it('validates at least one day must have office hours when office hours enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
            'office_hours_are_enabled' => true,
            'office_hours' => [],
        ])
        ->call('save')
        ->assertHasFormErrors(['office_hours_are_enabled']);
});

it('saves out of office configuration to user', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    $outOfOfficeStartsAt = now()->addDay();
    $outOfOfficeEndsAt = now()->addDays(7);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
            'out_of_office_is_enabled' => true,
            'out_of_office_starts_at' => $outOfOfficeStartsAt,
            'out_of_office_ends_at' => $outOfOfficeEndsAt,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->out_of_office_is_enabled)->toBeTrue();
    expect($user->out_of_office_starts_at->startOfSecond()->eq($outOfOfficeStartsAt->startOfSecond()))->toBeTrue();
    expect($user->out_of_office_ends_at->startOfSecond()->eq($outOfOfficeEndsAt->startOfSecond()))->toBeTrue();
});

it('validates out of office start date is required when out of office enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
            'out_of_office_is_enabled' => true,
            'out_of_office_starts_at' => null,
            'out_of_office_ends_at' => now()->addDays(7),
        ])
        ->call('save')
        ->assertHasFormErrors(['out_of_office_starts_at' => 'required']);
});

it('validates out of office end date is required when out of office enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => [
                'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
                'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ],
            'out_of_office_is_enabled' => true,
            'out_of_office_starts_at' => now()->addDay(),
            'out_of_office_ends_at' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['out_of_office_ends_at' => 'required']);
});

it('pre-fills form with existing booking page data', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create([
        'working_hours_are_enabled' => true,
        'working_hours' => [
            'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
        'office_hours' => [
            'monday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
    ]);

    Calendar::factory()->for($user)->create();

    $bookingPage = PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'existing-slug',
        'default_appointment_duration' => 45,
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->assertFormSet([
            'is_enabled' => true,
            'slug' => 'existing-slug',
            'default_appointment_duration' => 45,
            'working_hours_are_enabled' => true,
        ]);
});

it('generates default slug from user name when no booking page exists', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create([
        'name' => 'John Doe',
        'working_hours' => [
            'monday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
        'office_hours' => [
            'monday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->assertFormSet([
            'is_enabled' => false,
            'slug' => 'john-doe',
            'default_appointment_duration' => 30,
        ]);
});

it('hides view booking page action when booking page is disabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => false,
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->assertActionHidden('view_booking_page');
});

it('shows view booking page action when booking page is enabled', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'test-slug',
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->assertActionVisible('view_booking_page');
});

it('can save booking page with multiple working hours per day', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    $formWorkingHours = [
        'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '12:00'],
        'tuesday' => ['is_enabled' => true, 'starts_at' => '13:00', 'ends_at' => '17:00'],
        'wednesday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
        'thursday' => ['is_enabled' => true, 'starts_at' => '10:00', 'ends_at' => '16:00'],
        'friday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '15:00'],
        'saturday' => ['is_enabled' => false],
        'sunday' => ['is_enabled' => false],
    ];

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'test-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
            'working_hours' => $formWorkingHours,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->working_hours['monday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['tuesday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['wednesday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['thursday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['friday']['is_enabled'])->toBeTrue();
    expect($user->working_hours['saturday']['is_enabled'] ?? false)->toBeFalse();
    expect($user->working_hours['sunday']['is_enabled'] ?? false)->toBeFalse();
});

it('can update slug without affecting unique constraint for same user', function () {
    $user = User::factory()->licensed(LicenseType::RetentionCrm)->create([
        'working_hours_are_enabled' => true,
        'working_hours' => [
            'monday' => ['is_enabled' => true, 'starts_at' => '09:00', 'ends_at' => '17:00'],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
        'office_hours' => [
            'monday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'tuesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'wednesday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'thursday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'friday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'saturday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            'sunday' => ['is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ],
    ]);

    Calendar::factory()->for($user)->create();

    $bookingPage = PersonalBookingPage::factory()->create([
        'user_id' => $user->id,
        'is_enabled' => true,
        'slug' => 'original-slug',
    ]);

    actingAs($user);

    livewire(ManagePersonalBookingPage::class)
        ->fillForm([
            'is_enabled' => true,
            'slug' => 'original-slug',
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $bookingPage->refresh();

    expect($bookingPage->slug)->toBe('original-slug');
});
