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

namespace AdvisingApp\Form\Http\Controllers;

use AdvisingApp\Form\Actions\GenerateFormKitSchema;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidator;
use AdvisingApp\Form\Actions\ProcessSubmissionField;
use AdvisingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AdvisingApp\Form\Http\Requests\RegisterProspectRequest;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Notifications\AuthenticateFormNotification;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Http\Controllers\Controller;
use Closure;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class FormWidgetController extends Controller
{
    public function assets(Request $request, Form $form): JsonResponse
    {
        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/forms/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.forms.asset'),
            'entry' => route('widgets.forms.api.entry', ['form' => $form]),
            'js' => route('widgets.forms.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function asset(Request $request, string $file): StreamedResponse
    {
        $path = "widgets/forms/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }

    public function view(GenerateFormKitSchema $generateSchema, Form $form): JsonResponse
    {
        return response()->json([
            'name' => $form->name,
            'description' => $form->description,
            'is_authenticated' => $form->is_authenticated,
            ...($form->is_authenticated ? [
                'authentication_url' => URL::signedRoute(
                    name: 'widgets.forms.api.request-authentication',
                    parameters: ['form' => $form],
                ),
            ] : [
                'submission_url' => URL::signedRoute(
                    name: 'widgets.forms.api.submit',
                    parameters: ['form' => $form],
                ),
            ]),
            'recaptcha_enabled' => $form->recaptcha_enabled,
            ...($form->recaptcha_enabled ? [
                'recaptcha_site_key' => app(GoogleRecaptchaSettings::class)->site_key,
            ] : []),
            'schema' => $form->is_authenticated ? [] : $generateSchema($form),
            'primary_color' => collect(Color::all()[$form->primary_color ?? 'blue'])
                ->map(Color::convertToRgb(...))
                ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
            'rounding' => $form->rounding,
            'on_screen_response' => $form->on_screen_response,
        ]);
    }

    public function preview(GenerateFormKitSchema $generateSchema, Form $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->name,
                'description' => $form->description,
                'is_authenticated' => false,
                'recaptcha_enabled' => false,
                'schema' => $generateSchema($form),
                'primary_color' => collect(Color::all()[$form->primary_color ?? 'blue'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
                'rounding' => $form->rounding,
                'on_screen_response' => $form->on_screen_response,
            ]
        );
    }

    public function requestAuthentication(Request $request, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, Form $form): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $author = $resolveSubmissionAuthorFromEmail($data['email']);

        if (! $author) {
            if (! $form->generate_prospects) {
                throw ValidationException::withMessages([
                    'email' => 'A student with that email address could not be found. Please contact your system administrator.',
                ]);
            }

            return response()->json([
                'registrationAllowed' => true,
                'authentication_url' => URL::signedRoute(
                    name: 'widgets.forms.api.register-prospect',
                    parameters: ['form' => $form],
                ),
            ], 404);
        }

        $code = random_int(100000, 999999);

        $authentication = new FormAuthentication();
        $authentication->author()->associate($author);
        $authentication->submissible()->associate($form);
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $author->getAttributeValue($author::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'widgets.forms.api.authenticate',
                parameters: [
                    'form' => $form,
                    'authentication' => $authentication,
                ],
            ),
        ]);
    }

    public function authenticate(Request $request, GenerateFormKitSchema $generateSchema, Form $form, FormAuthentication $authentication): JsonResponse
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
                name: 'widgets.forms.api.submit',
                parameters: [
                    'authentication' => $authentication,
                    'form' => $authentication->submissible,
                ],
            ),
            'schema' => $generateSchema->withAuthor($authentication->author)($form),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidator $generateSubmissibleValidator,
        ProcessSubmissionField $processSubmissionField,
        Form $form,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = FormAuthentication::findOrFail($authentication);
        }

        /** @var ?FormAuthentication $authentication */
        if (
            $form->is_authenticated &&
            ($authentication?->isExpired() ?? true)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = $generateSubmissibleValidator($form);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        DB::beginTransaction();

        try {
            /** @var ?FormSubmission $submission */
            $submission = $authentication ? $form->submissions()
                ->requested()
                ->whereMorphedTo('author', $authentication->author)
                ->first() : null;

            $submission ??= $form->submissions()->make();

            if ($authentication) {
                $submission->author()->associate($authentication->author);

                $authentication->delete();
            }

            $submission->submitted_at = now();

            $submission->save();

            unset($data['recaptcha-token']);

            if ($form->is_wizard) {
                foreach ($form->steps as $step) {
                    $stepFields = $step->fields()->pluck('type', 'id')->all();

                    foreach ($data[$step->label] as $fieldId => $response) {
                        $processSubmissionField(
                            $submission,
                            $fieldId,
                            $response,
                            $stepFields,
                        );
                    }
                }
            } else {
                $formFields = $form->fields()->pluck('type', 'id')->all();

                foreach ($data as $fieldId => $response) {
                    $processSubmissionField(
                        $submission,
                        $fieldId,
                        $response,
                        $formFields,
                    );
                }
            }

            $submission->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'errors' => ['An error occurred while submitting this form.'],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Form submitted successfully.',
        ]);
    }

    public function registerProspect(RegisterProspectRequest $request, Form $form): JsonResponse
    {
        $data = $request->validated();

        $prospect = DB::transaction(function () use ($data): Prospect {
            $prospect = Prospect::query()
                ->make([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'preferred' => $data['preferred'],
                    'full_name' => "{$data['first_name']} {$data['last_name']}",
                    'birthdate' => $data['birthdate'],
                ]);

            $status = ProspectStatus::query()
                ->where('classification', SystemProspectClassification::New)
                ->first();

            if ($status) {
                $prospect->status()->associate($status);
            }

            $source = ProspectSource::query()
                ->where('name', 'Advising App')
                ->first();

            if ($source) {
                $prospect->source()->associate($source);
            }

            $prospect->save();

            $emailAddress = $prospect->emailAddresses()->create([
                'address' => $data['email'],
            ]);
            $prospect->primaryEmailAddress()->associate($emailAddress);

            $phoneNumber = $prospect->phoneNumbers()->create([
                'number' => $data['mobile'],
                'type' => 'Mobile',
                'can_receive_sms' => true,
            ]);
            $prospect->primaryPhoneNumber()->associate($phoneNumber);

            $address = $prospect->addresses()->create([
                'line_1' => $data['address'],
                'line_2' => $data['address_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal' => $data['postal'],
                'type' => 'Home',
            ]);
            $prospect->primaryAddress()->associate($address);

            $prospect->save();

            return $prospect;
        });

        $code = random_int(100000, 999999);

        $authentication = new FormAuthentication();
        $authentication->author()->associate($prospect);
        $authentication->submissible()->associate($form);
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $request->get('email') => $prospect->getAttributeValue($prospect::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$request->get('email')}.",
            'authentication_url' => URL::signedRoute(
                name: 'widgets.forms.api.authenticate',
                parameters: [
                    'form' => $form,
                    'authentication' => $authentication,
                ],
            ),
        ]);
    }

    public function uploadFormFiles(Request $request): JsonResponse
    {
        $this->validate($request, [
            'filename' => ['required', 'string'],
        ]);

        $filename = sprintf('%s.%s', Str::uuid(), str($request->get('filename'))->afterLast('.'));
        $path = "tmp/{$filename}";

        return response()->json([
            'filename' => $filename,
            'path' => $path,
            ...Storage::temporaryUploadUrl(
                $path,
                now()->addMinute(),
            ),
        ]);
    }
}
