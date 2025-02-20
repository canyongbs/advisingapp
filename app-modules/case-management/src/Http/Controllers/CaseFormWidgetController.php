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

namespace AdvisingApp\CaseManagement\Http\Controllers;

use AdvisingApp\CaseManagement\Actions\GenerateCaseFormKitSchema;
use AdvisingApp\CaseManagement\Models\CaseForm;
use AdvisingApp\CaseManagement\Models\CaseFormAuthentication;
use AdvisingApp\CaseManagement\Models\CaseFormSubmission;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidation;
use AdvisingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Notifications\AuthenticateFormNotification;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use App\Http\Controllers\Controller;
use Closure;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CaseFormWidgetController extends Controller
{
    public function view(GenerateCaseFormKitSchema $generateSchema, CaseForm $caseForm): JsonResponse
    {
        return response()->json(
            [
                'name' => $caseForm->name,
                'description' => $caseForm->description,
                'is_authenticated' => $caseForm->is_authenticated,
                ...($caseForm->is_authenticated ? [
                    'authentication_url' => URL::signedRoute(
                        name: 'case-forms.request-authentication',
                        parameters: ['caseForm' => $caseForm],
                        absolute: false
                    ),
                ] : [
                    'submission_url' => URL::signedRoute(
                        name: 'case-forms.submit',
                        parameters: ['caseForm' => $caseForm],
                        absolute: false
                    ),
                ]),
                'recaptcha_enabled' => $caseForm->recaptcha_enabled,
                ...($caseForm->recaptcha_enabled ? [
                    'recaptcha_site_key' => app(GoogleRecaptchaSettings::class)->site_key,
                ] : []),
                'schema' => $generateSchema($caseForm),
                'primary_color' => Color::all()[$caseForm->primary_color ?? 'blue'],
                'rounding' => $caseForm->rounding,
            ],
        );
    }

    public function requestAuthentication(Request $request, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, CaseForm $caseForm): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $author = $resolveSubmissionAuthorFromEmail($data['email']);

        if (! $author) {
            throw ValidationException::withMessages([
                'email' => 'A student with that email address could not be found. Please contact your system administrator.',
            ]);
        }

        $code = random_int(100000, 999999);

        $authentication = new CaseFormAuthentication();
        $authentication->author()->associate($author);
        $authentication->submissible()->associate($caseForm);
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $author->getAttributeValue($author::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'case-forms.authenticate',
                parameters: [
                    'caseForm' => $caseForm,
                    'authentication' => $authentication,
                ],
                absolute: false
            ),
        ]);
    }

    // public function authenticate(Request $request, CaseForm $caseForm, CaseFormAuthentication $authentication): JsonResponse
    public function authenticate(Request $request, CaseFormAuthentication $authentication): JsonResponse
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
                name: 'case-forms.submit',
                parameters: [
                    'authentication' => $authentication,
                    'caseForm' => $authentication->submissible,
                ],
                absolute: false
            ),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        CaseForm $caseForm,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = CaseFormAuthentication::findOrFail($authentication);
        }

        if (
            $caseForm->is_authenticated &&
            ($authentication?->isExpired() ?? true)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            $generateValidation($caseForm)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var ?CaseFormSubmission $submission */
        $submission = $authentication ? $caseForm->submissions()
            ->requested()
            ->whereMorphedTo('author', $authentication->author)
            ->first() : null;

        $submission ??= $caseForm->submissions()->make();

        $submission
            ->priority()
            ->associate($caseForm->type->priorities()->findOrFail($request->input('priority')));

        if ($authentication) {
            $submission->author()->associate($authentication->author);

            $authentication->delete();
        }

        $submission->submitted_at = now();

        $submission->save();

        $data = $validator->validated();

        unset($data['recaptcha-token']);

        if ($caseForm->is_wizard) {
            foreach ($caseForm->steps as $step) {
                $stepFields = $step->fields()->pluck('type', 'id')->all();

                foreach ($data[$step->label] as $fieldId => $response) {
                    $submission->fields()->attach(
                        $fieldId,
                        ['id' => Str::orderedUuid(), 'response' => $response],
                    );

                    if ($submission->author) {
                        continue;
                    }

                    if ($stepFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                        continue;
                    }

                    $author = $resolveSubmissionAuthorFromEmail($response);

                    if (! $author) {
                        continue;
                    }

                    $submission->author()->associate($author);
                }
            }
        } else {
            $formFields = $caseForm->fields()->pluck('type', 'id')->all();

            foreach ($data as $fieldId => $response) {
                $submission->fields()->attach(
                    $fieldId,
                    ['id' => Str::orderedUuid(), 'response' => $response],
                );

                if ($submission->author) {
                    continue;
                }

                if ($formFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                    continue;
                }

                $author = $resolveSubmissionAuthorFromEmail($response);

                if (! $author) {
                    continue;
                }

                $submission->author()->associate($author);
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Case Form submitted successfully.',
            ]
        );
    }
}
