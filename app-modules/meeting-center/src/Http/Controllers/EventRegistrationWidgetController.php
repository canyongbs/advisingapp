<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\MeetingCenter\Http\Controllers;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use AdvisingApp\Form\Actions\GenerateFormKitSchema;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidation;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormAuthentication;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AdvisingApp\Form\Notifications\AuthenticateEventRegistrationFormNotification;

class EventRegistrationWidgetController extends Controller
{
    public function view(GenerateFormKitSchema $generateSchema, EventRegistrationForm $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->event->title,
                'description' => $form->event->description,
                // TODO: Maybe get rid of this? It would never not be authenticated.
                'is_authenticated' => true,
                'authentication_url' => URL::signedRoute('event-registration.request-authentication', ['form' => $form]),
                'recaptcha_enabled' => $form->recaptcha_enabled,
                ...($form->recaptcha_enabled ? [
                    'recaptcha_site_key' => app(GoogleRecaptchaSettings::class)->site_key,
                ] : []),
                'schema' => $generateSchema($form),
                'primary_color' => Color::all()[$form->primary_color ?? 'blue'],
                'rounding' => $form->rounding,
            ],
        );
    }

    public function requestAuthentication(Request $request, EventRegistrationForm $form): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $attendee = EventAttendee::firstOrNew(
            [
                'email' => $data['email'],
                'event_id' => $form->event_id,
            ],
        );

        if (empty($attendee->status)) {
            $attendee->status = EventAttendeeStatus::Pending;
        }

        // TODO: When an EventAttendee is created, we should try and match it to an entity, perhaps in an observer.
        $attendee->save();

        $code = random_int(100000, 999999);

        $authentication = new EventRegistrationFormAuthentication();
        $authentication->author()->associate($attendee);
        $authentication->submissible()->associate($form);
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', $attendee->email)->notify(new AuthenticateEventRegistrationFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$attendee->email}.",
            'authentication_url' => URL::signedRoute('event-registration.authenticate', [
                'form' => $form,
                'authentication' => $authentication,
            ]),
        ]);
    }

    public function authenticate(Request $request, EventRegistrationForm $form, EventRegistrationFormAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', function (string $attribute, int $value, Closure $fail) use ($authentication) {
                if (Hash::check($value, $authentication->code)) {
                    return;
                }

                $fail('The provided code is invalid.');
            }],
        ]);

        return response()->json([
            'submission_url' => URL::signedRoute('event-registration.submit', [
                'authentication' => $authentication,
                'form' => $authentication->submissible,
            ]),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        EventRegistrationForm $form,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = EventRegistrationFormAuthentication::findOrFail($authentication);
        }

        if (
            ($authentication?->isExpired() ?? true)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            $generateValidation($form)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var EventRegistrationFormSubmission $submission */
        $submission = $form->submissions()->make();

        $submission->author()->associate($authentication->author);

        $authentication->delete();

        $submission->submitted_at = now();

        // TODO: Adjust the status of the EventAttendee to Attending or Not Attending based on the form submission.

        $submission->save();

        $data = $validator->validated();

        unset($data['recaptcha-token']);

        if ($form->is_wizard) {
            foreach ($form->steps as $step) {
                foreach ($data[$step->label] as $fieldId => $response) {
                    $submission->fields()->attach(
                        $fieldId,
                        ['id' => Str::orderedUuid(), 'response' => $response],
                    );
                }
            }
        } else {
            foreach ($data as $fieldId => $response) {
                $submission->fields()->attach(
                    $fieldId,
                    ['id' => Str::orderedUuid(), 'response' => $response],
                );
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Event registration submitted successfully.',
            ]
        );
    }
}
