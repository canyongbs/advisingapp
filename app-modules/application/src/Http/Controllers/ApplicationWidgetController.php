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

namespace AdvisingApp\Application\Http\Controllers;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationAuthentication;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Form\Actions\GenerateFormKitSchema;
use AdvisingApp\Form\Actions\GenerateSubmissibleValidation;
use AdvisingApp\Form\Actions\ProcessSubmissionField;
use AdvisingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AdvisingApp\Form\Http\Requests\RegisterProspectRequestForApplication;
use AdvisingApp\Form\Notifications\AuthenticateFormNotification;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\StudentDataModel\Models\Student;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationWidgetController extends Controller
{
    public function assets(Request $request, Application $application): JsonResponse
    {
        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/applications/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.applications.asset'),
            'entry' => route('widgets.applications.api.entry', ['application' => $application]),
            'js' => route('widgets.applications.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function asset(Request $request, string $file): StreamedResponse
    {
        $path = "widgets/applications/{$file}";

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

    public function view(Application $application): JsonResponse
    {
        return response()->json(
            [
                'name' => $application->title,
                'description' => $application->description,
                'authentication_url' => URL::signedRoute(
                    name: 'widgets.applications.api.request-authentication',
                    parameters: ['application' => $application],
                ),
                'primary_color' => collect(Color::all()[$application->primary_color ?? 'blue'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
                'rounding' => $application->rounding,
                'title_font_weight' => $application->title_font_weight,
                'title_color' => collect(Color::all()[$application->title_color ?? 'neutral'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
            ],
        );
    }

    public function preview(GenerateFormKitSchema $generateSchema, Application $application): JsonResponse
    {
        return response()->json(
            [
                'name' => $application->title,
                'description' => $application->description,
                'authentication_url' => null,
                'schema' => $generateSchema($application),
                'primary_color' => collect(Color::all()[$application->primary_color ?? 'blue'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
                'rounding' => $application->rounding,
                'title_font_weight' => $application->title_font_weight,
                'title_color' => collect(Color::all()[$application->title_color ?? 'neutral'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
            ],
        );
    }

    public function requestAuthentication(Request $request, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, Application $application): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $author = $resolveSubmissionAuthorFromEmail($data['email']);

        if (! $author) {
            if (! $application->should_generate_prospects) {
                throw ValidationException::withMessages([
                    'email' => 'A student with that email address could not be found. Please contact your system administrator.',
                ]);
            }

            return response()->json([
                'registrationAllowed' => true,
                'authentication_url' => URL::signedRoute(
                    name: 'widgets.applications.api.register-prospect',
                    parameters: ['application' => $application],
                ),
            ], 404);
        }

        $code = random_int(100000, 999999);

        $authentication = new ApplicationAuthentication();
        $authentication->author()->associate($author);
        $authentication->submissible()->associate($application);
        $authentication->code = Hash::make((string) $code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $author->getAttributeValue($author::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'widgets.applications.api.authenticate',
                parameters: [
                    'application' => $application,
                    'authentication' => $authentication,
                ],
            ),
        ]);
    }

    public function authenticate(Request $request, GenerateFormKitSchema $generateSchema, Application $application, ApplicationAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', function (string $attribute, int $value, Closure $fail) use ($authentication) {
                if (Hash::check((string) $value, $authentication->code)) {
                    return;
                }

                $fail('The provided code is invalid.');
            }],
        ]);

        $author = $authentication->author;

        assert($author instanceof Prospect || $author instanceof Student || $author === null);

        return response()->json([
            'submission_url' => URL::signedRoute(
                name: 'widgets.applications.api.submit',
                parameters: [
                    'authentication' => $authentication,
                    'application' => $authentication->submissible,
                ],
            ),
            'schema' => $generateSchema->withAuthor($author)($application),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ProcessSubmissionField $processSubmissionField,
        Application $application,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = ApplicationAuthentication::findOrFail($authentication);
        }

        /** @var ?ApplicationAuthentication $authentication */
        if (
            ($authentication?->isExpired() ?? true)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            $generateValidation($application)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var ApplicationSubmission $submission */
        $submission = $application->submissions()->make();

        if ($authentication) {
            $submission->author()->associate($authentication->author);

            $authentication->delete();
        }

        $submission->save();

        $data = $validator->validated();

        if ($application->is_wizard) {
            foreach ($application->steps as $step) {
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
            $applicationFields = $application->fields()->pluck('type', 'id')->all();

            foreach ($data as $fieldId => $response) {
                $processSubmissionField(
                    $submission,
                    $fieldId,
                    $response,
                    $applicationFields,
                );
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Application submitted successfully.',
            ]
        );
    }

    public function registerProspect(RegisterProspectRequestForApplication $request, Application $application): JsonResponse
    {
        $data = $request->validated();

        $prospect = DB::transaction(function () use ($data): Prospect {
            $prospect = Prospect::query()
                ->make([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'preferred' => $data['preferred'] ?? null,
                    'full_name' => "{$data['first_name']} {$data['last_name']}",
                    'birthdate' => $data['birthdate'] ?? null,
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

            if (
                isset($data['address']) ||
                isset($data['address_2']) ||
                isset($data['city']) ||
                isset($data['state']) ||
                isset($data['postal'])
            ) {
                $address = $prospect->addresses()->create([
                    'line_1' => $data['address'] ?? null,
                    'line_2' => $data['address_2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'state' => $data['state'] ?? null,
                    'postal' => $data['postal'] ?? null,
                    'type' => 'Home',
                ]);
                $prospect->primaryAddress()->associate($address);
            }

            $prospect->save();

            return $prospect;
        });

        $code = random_int(100000, 999999);

        $authentication = new ApplicationAuthentication();
        $authentication->author()->associate($prospect);
        $authentication->submissible()->associate($application);
        $authentication->code = Hash::make((string) $code);
        $authentication->save();

        Notification::route('mail', [
            $request->get('email') => $prospect->getAttributeValue($prospect::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'widgets.applications.api.authenticate',
                parameters: [
                    'application' => $application,
                    'authentication' => $authentication,
                ],
            ),
        ]);
    }
}
