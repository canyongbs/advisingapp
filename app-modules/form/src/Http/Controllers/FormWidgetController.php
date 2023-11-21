<?php

namespace Assist\Form\Http\Controllers;

use Closure;
use Illuminate\Support\Str;
use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Assist\Form\Models\FormAuthentication;
use Illuminate\Support\Facades\Notification;
use Assist\Form\Actions\GenerateFormKitSchema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Assist\Form\Actions\GenerateFormValidation;
use Assist\Form\Actions\ResolveSubmissionAuthorFromEmail;
use Assist\Form\Notifications\AuthenticateFormNotification;
use Assist\Form\Filament\Blocks\EducatableEmailFormFieldBlock;

class FormWidgetController extends Controller
{
    public function view(GenerateFormKitSchema $generateSchema, Form $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->name,
                'description' => $form->description,
                'is_authenticated' => $form->is_authenticated,
                ...($form->is_authenticated ? [
                    'authentication_url' => URL::signedRoute('forms.request-authentication', ['form' => $form]),
                ] : [
                    'submission_url' => URL::signedRoute('forms.submit', ['form' => $form]),
                ]),
                'schema' => $generateSchema($form),
                'primary_color' => Color::all()[$form->primary_color ?? 'blue'],
                'rounding' => $form->rounding,
            ],
        );
    }

    public function requestAuthentication(Request $request, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, Form $form): JsonResponse
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

        $authentication = new FormAuthentication();
        $authentication->author()->associate($author);
        $authentication->form()->associate($form);
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $author->getAttributeValue($author::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute('forms.authenticate', [
                'form' => $form,
                'authentication' => $authentication,
            ]),
        ]);
    }

    public function authenticate(Request $request, Form $form, FormAuthentication $authentication): JsonResponse
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
            'submission_url' => URL::signedRoute('forms.submit', [
                'authentication' => $authentication,
                'form' => $authentication->form,
            ]),
        ]);
    }

    public function store(
        Request $request,
        GenerateFormValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        Form $form,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = FormAuthentication::findOrFail($authentication);
        }

        if (
            $form->is_authenticated &&
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

        $submission = $form->submissions()->make();

        if ($authentication) {
            $submission->author()->associate($authentication->author);

            $authentication->delete();
        }

        $submission->save();

        $data = $validator->validated();

        if ($form->is_wizard) {
            foreach ($form->steps as $step) {
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
            $formFields = $form->fields()->pluck('type', 'id')->all();

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
                'message' => 'Form submitted successfully.',
            ]
        );
    }
}
