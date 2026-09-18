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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Jobs\DeliverEventRegistrationFormSubmissionRequestByEmail;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EventsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\EventRegistrationRequestsFeature;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('requires event_id to submit the request', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => null,
        ])
        ->assertHasTableActionErrors(['event_id' => 'required']);
});

it('can request an event registration via email', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
            'request_note' => 'Please complete your registration.',
        ])
        ->assertHasNoTableActionErrors();

    $attendee = EventAttendee::query()
        ->where('email', $student->primaryEmailAddress->address)
        ->where('event_id', $event->id)
        ->first();

    expect($attendee)->not->toBeNull();

    $submission = $attendee->submissions()->first();

    expect($submission)->not->toBeNull()
        ->and($submission->request_method)->toBe(FormSubmissionRequestDeliveryMethod::Email)
        ->and($submission->request_note)->toBe('Please complete your registration.')
        ->and($submission->requester_id)->toBe($user->id)
        ->and($submission->submitted_at)->toBeNull()
        ->and($submission->canceled_at)->toBeNull();
});

it('allows request_note to be optional', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
            'request_note' => null,
        ])
        ->assertHasNoTableActionErrors();

    $attendee = EventAttendee::query()
        ->where('email', $student->primaryEmailAddress->address)
        ->where('event_id', $event->id)
        ->first();

    expect($attendee->submissions()->first()->request_note)->toBeNull();
});

it('reuses an existing attendee and requested submission for the same event instead of creating new ones', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    $existingAttendee = EventAttendee::factory()->create([
        'email' => $student->primaryEmailAddress->address,
        'event_id' => $event->id,
        'status' => EventAttendeeStatus::Invited,
    ]);

    $existingSubmission = EventRegistrationFormSubmission::factory()->create([
        'form_id' => $event->eventRegistrationForm->id,
        'attendee_status' => EventAttendeeStatus::Invited,
        'submitted_at' => null,
        'canceled_at' => null,
        'request_note' => 'Old note',
    ]);
    $existingSubmission->author()->associate($existingAttendee);
    $existingSubmission->save();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
            'request_note' => 'Updated note',
        ])
        ->assertHasNoTableActionErrors();

    expect(EventAttendee::query()->where('email', $student->primaryEmailAddress->address)->where('event_id', $event->id)->count())->toBe(1);

    $attendee = EventAttendee::query()->where('email', $student->primaryEmailAddress->address)->where('event_id', $event->id)->first();

    expect($attendee->id)->toBe($existingAttendee->id)
        ->and($attendee->submissions()->count())->toBe(1);

    $submission = $attendee->submissions()->first();

    expect($submission->id)->toBe($existingSubmission->id)
        ->and($submission->request_note)->toBe('Updated note')
        ->and($submission->requester_id)->toBe($user->id);
});

it('creates a new submission when the existing one is already submitted', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    $attendee = EventAttendee::factory()->create([
        'email' => $student->primaryEmailAddress->address,
        'event_id' => $event->id,
        'status' => EventAttendeeStatus::Attending,
    ]);

    $submitted = EventRegistrationFormSubmission::factory()->create([
        'form_id' => $event->eventRegistrationForm->id,
        'attendee_status' => EventAttendeeStatus::Attending,
        'submitted_at' => now(),
    ]);
    $submitted->author()->associate($attendee);
    $submitted->save();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
        ])
        ->assertHasNoTableActionErrors();

    expect($attendee->submissions()->count())->toBe(2);
});

it('associates the current authenticated user as the requester', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
        ])
        ->assertHasNoTableActionErrors();

    $attendee = EventAttendee::query()
        ->where('email', $student->primaryEmailAddress->address)
        ->where('event_id', $event->id)
        ->first();

    $submission = $attendee->submissions()->first();

    expect($submission->requester_id)->toBe($user->id)
        ->and($submission->requester->is($user))->toBeTrue();
});

it('dispatches the delivery job after creating the submission', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
        ])
        ->assertHasNoTableActionErrors();

    Queue::assertPushed(DeliverEventRegistrationFormSubmissionRequestByEmail::class);
});

it('sends a success notification after the request is sent', function () {
    Queue::fake();

    asSuperAdmin();

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->callTableAction('request', data: [
            'event_id' => $event->id,
        ])
        ->assertHasNoTableActionErrors()
        ->assertNotified('Event registration request sent');
});

it('hides the request action when the feature flag is inactive', function () {
    EventRegistrationRequestsFeature::deactivate();

    asSuperAdmin();

    $student = Student::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionHidden('request');
});

it('hides the request action from a user without the event_attendee.create ability', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('event_attendee.view-any');

    actingAs($user);

    $student = Student::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionHidden('request');
});

it('allows a user with the event_attendee.create ability to request an event registration form submission', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('event_attendee.view-any');
    $user->givePermissionTo('event_attendee.create');

    actingAs($user);

    $student = Student::factory()->create();
    $event = Event::factory()->create();

    livewire(EventsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionVisible('request')
        ->callTableAction('request', data: [
            'event_id' => $event->id,
        ])
        ->assertHasNoTableActionErrors();
});
