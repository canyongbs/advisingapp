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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationAuthentication;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AdvisingApp\Prospect\Models\Prospect;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;
use function Pest\Laravel\withoutMiddleware;

beforeEach(function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();
});

test('view endpoint returns allow_view_past_submissions true when toggle is on', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $response = get(route('widgets.applications.api.entry', ['application' => $application]))
        ->assertSuccessful();

    expect($response->json('allow_view_past_submissions'))->toBeTrue();
});

test('view endpoint returns allow_view_past_submissions false when toggle is off', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => false,
    ]);

    $response = get(route('widgets.applications.api.entry', ['application' => $application]))
        ->assertSuccessful();

    expect($response->json('allow_view_past_submissions'))->toBeFalse();
});

test('authenticate returns past_submissions_count and url when toggle is on and submissions exist', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $code = 123456;

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make($code),
    ]);

    ApplicationSubmission::factory()->count(3)->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    $response = post(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
            'code' => $code,
        ],
    ))->assertSuccessful();

    expect($response->json('allow_view_past_submissions'))->toBeTrue();
    expect($response->json('past_submissions_count'))->toBe(3);
    expect($response->json('past_submissions_url'))->not->toBeNull();
    expect($response->json('schema'))->toBeArray();
    expect($response->json('submission_url'))->not->toBeNull();
});

test('authenticate returns zero past_submissions_count when toggle is on but no submissions exist', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $code = 123456;

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make($code),
    ]);

    $response = post(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
            'code' => $code,
        ],
    ))->assertSuccessful();

    expect($response->json('allow_view_past_submissions'))->toBeTrue();
    expect($response->json('past_submissions_count'))->toBe(0);
    expect($response->json('past_submissions_url'))->toBeNull();
});

test('authenticate returns zero past_submissions_count when toggle is off', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => false,
    ]);

    $prospect = Prospect::factory()->create();

    $code = 123456;

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make($code),
    ]);

    ApplicationSubmission::factory()->count(2)->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    $response = post(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
            'code' => $code,
        ],
    ))->assertSuccessful();

    expect($response->json('allow_view_past_submissions'))->toBeFalse();
    expect($response->json('past_submissions_count'))->toBe(0);
    expect($response->json('past_submissions_url'))->toBeNull();
});

test('getPastSubmissions returns paginated past submissions', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    ApplicationSubmission::factory()->count(3)->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    $response = get(URL::signedRoute(
        name: 'widgets.applications.api.get-past-submissions',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
        ],
    ))->assertSuccessful();

    expect($response->json('past_submissions'))->toHaveCount(3);
    expect($response->json('meta.total'))->toBe(3);
    expect($response->json('meta.current_page'))->toBe(1);

    $firstSubmission = $response->json('past_submissions.0');
    expect($firstSubmission)->toHaveKeys(['id', 'submitted_at', 'view_url']);
});

test('getPastSubmissions returns empty when toggle is off', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => false,
    ]);

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    $response = get(URL::signedRoute(
        name: 'widgets.applications.api.get-past-submissions',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
        ],
    ))->assertSuccessful();

    expect($response->json('past_submissions'))->toBeEmpty();
});

test('getPastSubmissions does not return submissions from other authors', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();
    $otherProspect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    // Submissions from the authenticated author
    ApplicationSubmission::factory()->count(2)->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    // Submissions from a different author
    ApplicationSubmission::factory()->count(3)->create([
        'application_id' => $application->id,
        'author_type' => $otherProspect->getMorphClass(),
        'author_id' => $otherProspect->getKey(),
    ]);

    $response = get(URL::signedRoute(
        name: 'widgets.applications.api.get-past-submissions',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
        ],
    ))->assertSuccessful();

    expect($response->json('past_submissions'))->toHaveCount(2);
    expect($response->json('meta.total'))->toBe(2);
});

test('getPastSubmissions returns 401 with expired authentication', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    ApplicationAuthentication::withoutTimestamps(
        fn () => ApplicationAuthentication::where('id', $authentication->id)->update(['created_at' => now()->subDays(2)])
    );
    $authentication->refresh();

    get(URL::signedRoute(
        name: 'widgets.applications.api.get-past-submissions',
        parameters: [
            'application' => $application,
            'authentication' => $authentication,
        ],
    ))->assertUnauthorized();
});

test('getPastSubmissions returns 401 without authentication parameter', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    get(URL::signedRoute(
        name: 'widgets.applications.api.get-past-submissions',
        parameters: [
            'application' => $application,
        ],
    ))->assertUnauthorized();
});

test('getSubmission returns submission detail data', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    $response = get(URL::signedRoute(
        name: 'widgets.applications.api.get-submission',
        parameters: [
            'application' => $application,
            'submission' => $submission,
            'authentication' => $authentication,
        ],
    ))->assertSuccessful();

    expect($response->json('id'))->toBe($submission->getKey());
    expect($response->json('submitted_at'))->not->toBeNull();
    expect($response->json('is_wizard'))->toBeFalse();
    expect($response->json('fields'))->toBeArray();
});

test('getSubmission returns 401 with expired authentication', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    ApplicationAuthentication::withoutTimestamps(
        fn () => ApplicationAuthentication::where('id', $authentication->id)->update(['created_at' => now()->subDays(2)])
    );
    $authentication->refresh();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    get(URL::signedRoute(
        name: 'widgets.applications.api.get-submission',
        parameters: [
            'application' => $application,
            'submission' => $submission,
            'authentication' => $authentication,
        ],
    ))->assertUnauthorized();
});

test('getSubmission returns 403 when author does not match', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);

    $prospect = Prospect::factory()->create();
    $otherProspect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'author_type' => $otherProspect->getMorphClass(),
        'author_id' => $otherProspect->getKey(),
    ]);

    get(URL::signedRoute(
        name: 'widgets.applications.api.get-submission',
        parameters: [
            'application' => $application,
            'submission' => $submission,
            'authentication' => $authentication,
        ],
    ))->assertForbidden();
});

test('getSubmission returns 404 when submission does not belong to application', function () {
    $application = Application::factory()->create([
        'allow_view_past_submissions' => true,
    ]);
    $otherApplication = Application::factory()->create();

    $prospect = Prospect::factory()->create();

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
        'code' => Hash::make('123456'),
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $otherApplication->id,
        'author_type' => $prospect->getMorphClass(),
        'author_id' => $prospect->getKey(),
    ]);

    get(URL::signedRoute(
        name: 'widgets.applications.api.get-submission',
        parameters: [
            'application' => $application,
            'submission' => $submission,
            'authentication' => $authentication,
        ],
    ))->assertNotFound();
});
