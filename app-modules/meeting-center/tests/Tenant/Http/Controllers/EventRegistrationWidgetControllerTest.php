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

use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Http\Middleware\EnsureEventRegistrationFormIsEmbeddableAndAuthorized;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormAuthentication;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;

beforeEach(function (): void {
    withoutMiddleware([EnsureEventRegistrationFormIsEmbeddableAndAuthorized::class]);
});

/**
 * Creates an event with a field-less registration form and an authenticated attendee with the given status.
 *
 * @return array{Event, EventAttendee, EventRegistrationFormAuthentication}
 */
$createAuthenticatedAttendee = function (EventAttendeeStatus $status): array {
    $event = Event::factory()->create();

    $form = $event->eventRegistrationForm;
    $form->update(['is_wizard' => false]);
    $form->fields()->delete();
    $form->steps()->delete();

    $attendee = EventAttendee::factory()->create([
        'event_id' => $event->getKey(),
        'status' => $status,
    ]);

    $authentication = new EventRegistrationFormAuthentication();
    $authentication->author()->associate($attendee);
    $authentication->submissible()->associate($form);
    $authentication->forceFill(['code' => Hash::make('123456')]);
    $authentication->save();

    return [$event, $attendee, $authentication];
};

$submitUrl = fn (Event $event, EventRegistrationFormAuthentication $authentication): string => URL::signedRoute(
    name: 'widgets.event-registration.api.submit',
    parameters: ['event' => $event, 'authentication' => $authentication],
);

describe('authenticate', function () use ($createAuthenticatedAttendee): void {
    it('returns whether the attendance question is required', function (EventAttendeeStatus $status, bool $isRequired) use ($createAuthenticatedAttendee): void {
        [$event, , $authentication] = $createAuthenticatedAttendee($status);

        postJson(URL::signedRoute(
            name: 'widgets.event-registration.api.authenticate',
            parameters: ['event' => $event, 'authentication' => $authentication],
        ), ['code' => 123456])
            ->assertSuccessful()
            ->assertJson(['is_attendance_question_required' => $isRequired]);
    })->with([
        'invited' => [EventAttendeeStatus::Invited, true],
        'registering through the public link for the first time' => [EventAttendeeStatus::Pending, false],
        'already answered `yes`' => [EventAttendeeStatus::Attending, true],
        'already answered `no`' => [EventAttendeeStatus::NotAttending, true],
    ]);

    it('requires the attendance question from a first time registrant who was sent a registration request', function () use ($createAuthenticatedAttendee): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Pending);

        EventRegistrationFormSubmission::factory()->create([
            'form_id' => $event->eventRegistrationForm->getKey(),
            'event_attendee_id' => $attendee->getKey(),
            'attendee_status' => EventAttendeeStatus::Invited,
            'submitted_at' => null,
            'canceled_at' => null,
        ]);

        postJson(URL::signedRoute(
            name: 'widgets.event-registration.api.authenticate',
            parameters: ['event' => $event, 'authentication' => $authentication],
        ), ['code' => 123456])
            ->assertSuccessful()
            ->assertJson(['is_attendance_question_required' => true]);
    });
});

describe('store', function () use ($createAuthenticatedAttendee, $submitUrl): void {
    it('registers an invited attendee who answers `no` as not attending', function () use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Invited);

        postJson($submitUrl($event, $authentication), ['attending' => 'no'])
            ->assertSuccessful();

        expect(EventRegistrationFormSubmission::query()->where('event_attendee_id', $attendee->getKey())->sole()->attendee_status)->toBe(EventAttendeeStatus::NotAttending)
            ->and($attendee->refresh()->status)->toBe(EventAttendeeStatus::NotAttending);
    });

    it('requires an invited attendee to answer the attendance question', function () use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Invited);

        postJson($submitUrl($event, $authentication), [])
            ->assertUnprocessable()
            ->assertJsonPath('errors.attending.0', 'The attending field is required.');

        expect(EventRegistrationFormSubmission::query()->where('event_attendee_id', $attendee->getKey())->exists())->toBeFalse()
            ->and($attendee->refresh()->status)->toBe(EventAttendeeStatus::Invited);
    });

    it('registers a first time registrant as attending without asking', function (array $data) use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Pending);

        postJson($submitUrl($event, $authentication), $data)
            ->assertSuccessful();

        expect(EventRegistrationFormSubmission::query()->where('event_attendee_id', $attendee->getKey())->sole()->attendee_status)->toBe(EventAttendeeStatus::Attending)
            ->and($attendee->refresh()->status)->toBe(EventAttendeeStatus::Attending);
    })->with([
        'without an answer' => [[]],
        'while sending `no`' => [['attending' => 'no']],
    ]);

    it('lets an attendee who already responded change their answer', function (EventAttendeeStatus $status, string $answer, EventAttendeeStatus $expectedStatus) use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee($status);

        postJson($submitUrl($event, $authentication), ['attending' => $answer])
            ->assertSuccessful();

        expect(EventRegistrationFormSubmission::query()->where('event_attendee_id', $attendee->getKey())->sole()->attendee_status)->toBe($expectedStatus)
            ->and($attendee->refresh()->status)->toBe($expectedStatus);
    })->with([
        'cancelling a registration' => [EventAttendeeStatus::Attending, 'no', EventAttendeeStatus::NotAttending],
        'attending after declining' => [EventAttendeeStatus::NotAttending, 'yes', EventAttendeeStatus::Attending],
    ]);

    it('requires an attendee who already responded to answer the attendance question', function () use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Attending);

        postJson($submitUrl($event, $authentication), [])
            ->assertUnprocessable()
            ->assertJsonPath('errors.attending.0', 'The attending field is required.');

        expect($attendee->refresh()->status)->toBe(EventAttendeeStatus::Attending);
    });

    it('still requires the registration form fields from a first time registrant', function () use ($createAuthenticatedAttendee, $submitUrl): void {
        [$event, $attendee, $authentication] = $createAuthenticatedAttendee(EventAttendeeStatus::Pending);

        $field = $event->eventRegistrationForm->fields()->create([
            'label' => 'First name',
            'type' => 'text_input',
            'is_required' => true,
            'config' => [],
        ]);

        postJson($submitUrl($event, $authentication), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($field->getKey(), 'errors');

        expect(EventRegistrationFormSubmission::query()->where('event_attendee_id', $attendee->getKey())->exists())->toBeFalse()
            ->and($attendee->refresh()->status)->toBe(EventAttendeeStatus::Pending);
    });
});
