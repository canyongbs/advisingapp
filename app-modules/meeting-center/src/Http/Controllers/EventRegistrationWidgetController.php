<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Form\Actions\GenerateSubmissibleValidation;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AdvisingApp\MeetingCenter\Actions\GenerateEventRegistrationFormKitSchema;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormAuthentication;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use AdvisingApp\MeetingCenter\Notifications\AuthenticateEventRegistrationFormNotification;
use App\Http\Controllers\Controller;
use Closure;
use Exception;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EventRegistrationWidgetController extends Controller
{
    public function view(GenerateEventRegistrationFormKitSchema $generateSchema, Event $event): JsonResponse
    {
        $form = $event->eventRegistrationForm;

        return response()->json(
            [
                'name' => $form->event->title,
                'description' => $form->event->description,
                'is_authenticated' => true,
                'authentication_url' => URL::signedRoute(
                    name: 'event-registration.request-authentication',
                    parameters: ['event' => $event],
                    absolute: false,
                ),
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

    public function requestAuthentication(Request $request, Event $event): JsonResponse
    {
        $form = $event->eventRegistrationForm;

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
            'authentication_url' => URL::signedRoute(
                name: 'event-registration.authenticate',
                parameters: [
                    'event' => $event,
                    'authentication' => $authentication,
                ],
                absolute: false,
            ),
        ]);
    }

    public function authenticate(Request $request, Event $event, EventRegistrationFormAuthentication $authentication): JsonResponse
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
            'submission_url' => URL::signedRoute(
                name: 'event-registration.submit',
                parameters: [
                    'authentication' => $authentication,
                    'event' => $authentication->submissible->event,
                ],
                absolute: false,
            ),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        Event $event,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $form = $event->eventRegistrationForm;

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
                [
                    'attending' => ['required', 'in:yes,no'],
                    ...$request->get('attending') === 'yes' ? $generateValidation($form) : [],
                ]
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

            $submission->attendee_status = $request->get('attending') === 'yes' ? EventAttendeeStatus::Attending : EventAttendeeStatus::NotAttending;

            $submission->save();

            $authentication->author->update(['status' => $request->get('attending') === 'yes' ? EventAttendeeStatus::Attending : EventAttendeeStatus::NotAttending]);

            $data = $validator->validated();

            if ($data['attending'] === 'yes') {
                unset($data['recaptcha-token'], $data['attending']);

                if ($form->is_wizard) {
                    foreach ($form->steps as $step) {
                        if (! array_key_exists($step->label, $data)) {
                            continue;
                        }

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
            }

            DB::commit();
        } catch (Exception $e) {
            // TODO: Tag and report this exception. Send the tag to the frontend as a reference.

            DB::rollBack();

            throw $e;
        }

        return response()->json(
            [
                'message' => 'Event registration submitted successfully.',
            ]
        );
    }
}
