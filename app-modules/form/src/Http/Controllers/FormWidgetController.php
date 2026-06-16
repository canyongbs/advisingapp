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

namespace AdvisingApp\Form\Http\Controllers;

use AdvisingApp\Form\Actions\CreateProspectFromSubmission;
use AdvisingApp\Form\Actions\GenerateFormKitSchema;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidator;
use AdvisingApp\Form\Actions\GenerateSubmissionViewData;
use AdvisingApp\Form\Actions\ProcessSubmissionField;
use AdvisingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AdvisingApp\Form\Http\Requests\RegisterProspectRequest;
use AdvisingApp\Form\Jobs\SendFormNotificationJob;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Notifications\AuthenticateFormNotification;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\FormVersioningFeature;
use App\Features\PastSubmissionsFeature;
use App\Features\PhoneNumberLookupFeature;
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
        $form = $this->resolveToLatestVersion($form);

        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/forms/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.forms.asset'),
            'entry' => $request->boolean('preview')
                ? route('forms.api.preview', ['form' => $form])
                : route('widgets.forms.api.entry', ['form' => $form]),
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
        $form = $this->resolveToLatestVersion($form);

        return response()->json([
            'name' => $form->title,
            'description' => $form->description,
            'is_authenticated' => $form->is_authenticated,
            'allow_view_past_submissions' => $form->is_authenticated && $form->allow_view_past_submissions,
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
                ->map(fn(string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
            'rounding' => $form->rounding,
            'on_screen_response' => $form->on_screen_response,
            'title_font_weight' => $form->title_font_weight,
            'title_color' => collect(Color::all()[$form->title_color ?? 'neutral'])
                ->map(Color::convertToRgb(...))
                ->map(fn(string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
        ]);
    }

    public function preview(GenerateFormKitSchema $generateSchema, Form $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->title,
                'description' => $form->description,
                'is_authenticated' => $form->is_authenticated,
                'recaptcha_enabled' => false,
                'schema' => $generateSchema($form),
                'primary_color' => collect(Color::all()[$form->primary_color ?? 'blue'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn(string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
                'rounding' => $form->rounding,
                'on_screen_response' => $form->on_screen_response,
                'title_font_weight' => $form->title_font_weight,
                'title_color' => collect(Color::all()[$form->title_color ?? 'neutral'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn(string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
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

        $author = $authentication->author;

        assert($author instanceof Prospect || $author instanceof Student || $author === null);

        $pastSubmissionsCount = 0;
        $pastSubmissionsUrl = null;

        if (PastSubmissionsFeature::active() && $form->allow_view_past_submissions && $author) {
            $pastSubmissionsCount = $form->submissions()
                ->submitted()
                ->whereMorphedTo('author', $author)
                ->count();

            if ($pastSubmissionsCount > 0) {
                $pastSubmissionsUrl = URL::signedRoute(
                    name: 'widgets.forms.api.get-past-submissions',
                    parameters: [
                        'form' => $form,
                        'authentication' => $authentication,
                    ],
                );
            }
        }

        return response()->json([
            'submission_url' => URL::signedRoute(
                name: 'widgets.forms.api.submit',
                parameters: [
                    'authentication' => $authentication,
                    'form' => $authentication->submissible,
                ],
            ),
            'schema' => $generateSchema->withAuthor($author)($form),
            'allow_view_past_submissions' => PastSubmissionsFeature::active() && $form->allow_view_past_submissions,
            'past_submissions_count' => $pastSubmissionsCount,
            'past_submissions_url' => $pastSubmissionsUrl,
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidator $generateSubmissibleValidator,
        ProcessSubmissionField $processSubmissionField,
        CreateProspectFromSubmission $createProspectFromSubmission,
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

                    foreach ($data[$step->label] ?? [] as $fieldId => $response) {
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

            if (! $form->is_authenticated && $form->generate_prospects && ! $submission->author) {
                $createProspectFromSubmission($submission);
            }

            $submission->save();
            SendFormNotificationJob::dispatch($form, $submission)->afterCommit();

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
                ...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => true] : []),
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

    public function getPastSubmissions(
        Request $request,
        Form $form,
    ): JsonResponse {
        if (! PastSubmissionsFeature::active()) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = FormAuthentication::findOrFail($authentication);
        }

        if (! $authentication || $authentication->isExpired()) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $author = $authentication->author;

        assert($author instanceof Prospect || $author instanceof Student || $author === null);

        if (! $form->allow_view_past_submissions || ! $author) {
            return response()->json([
                'past_submissions' => [],
            ]);
        }

        $pastSubmissions = $form->submissions()
            ->submitted()
            ->whereMorphedTo('author', $author)
            ->orderByDesc('submitted_at')
            ->paginate($request->query('per_page', 10));

        $items = $pastSubmissions->map(fn(FormSubmission $submission) => [
            'id' => $submission->getKey(),
            'submitted_at' => $submission->submitted_at->toIso8601String(),
            'view_url' => URL::signedRoute(
                name: 'widgets.forms.api.get-submission',
                parameters: [
                    'form' => $form,
                    'submission' => $submission,
                    'authentication' => $authentication,
                ],
            ),
        ])->all();

        return response()->json([
            'past_submissions' => $items,
            'meta' => [
                'current_page' => $pastSubmissions->currentPage(),
                'last_page' => $pastSubmissions->lastPage(),
                'per_page' => $pastSubmissions->perPage(),
                'total' => $pastSubmissions->total(),
            ],
        ]);
    }

    public function getSubmission(
        GenerateSubmissionViewData $generateSubmissionViewData,
        GenerateFormKitSchema $generateSchema,
        Form $form,
        FormSubmission $submission,
    ): JsonResponse {
        if (! PastSubmissionsFeature::active()) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $authentication = request()->query('authentication');

        if (filled($authentication)) {
            $authentication = FormAuthentication::findOrFail($authentication);
        }

        if (! $authentication || $authentication->isExpired()) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        abort_unless($submission->form_id === $form->getKey(), Response::HTTP_NOT_FOUND);

        $author = $authentication->author;

        abort_unless(
            $submission->author_type === $author?->getMorphClass()
                && $submission->author_id === $author?->getKey(),
            Response::HTTP_FORBIDDEN,
        );

        return response()->json(
            $generateSubmissionViewData($submission, $generateSchema),
        );
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

    private function resolveToLatestVersion(Form $form): Form
    {
        if (! FormVersioningFeature::active()) {
            return $form;
        }

        if (! $form->isArchived()) {
            return $form;
        }

        return Form::query()->where('root_id', $form->root_id)
            ->whereNull('archived_at')
            ->first() ?? $form;
    }
}
