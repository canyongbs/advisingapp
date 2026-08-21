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
use AdvisingApp\Application\Models\ApplicationField;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AdvisingApp\Prospect\Models\Prospect;
use App\Settings\LicenseSettings;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;
use function Pest\Laravel\withoutMiddleware;
use function Tests\asSuperAdmin;

test('define is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    get(URL::signedRoute(
        name: 'widgets.applications.api.entry',
        parameters: ['application' => $application],
    ))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    get(URL::signedRoute(
        name: 'widgets.applications.api.entry',
        parameters: ['application' => $application],
    ))
        ->assertSuccessful();
});

test('request-authentication is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    $prospect = Prospect::factory()->create();

    post(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $prospect->primaryEmailAddress->address],
    ))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $prospect->primaryEmailAddress->address],
    ))
        ->assertSuccessful();
});

test('authenticate is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    $code = random_int(100000, 999999);

    $authorization = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'code' => Hash::make($code),
    ]);

    post(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authorization,  'code' => $code],
    ))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authorization, 'code' => $code],
    ))
        ->assertSuccessful();
});

test('submit is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    $application->content = [];

    $application->save();

    $application->fields()->delete();

    $authorization = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
    ]);

    post(URL::signedRoute(
        name: 'widgets.applications.api.submit',
        parameters: ['application' => $application, 'authentication' => $authorization],
    ))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute(
        name: 'widgets.applications.api.submit',
        parameters: ['application' => $application, 'authentication' => $authorization],
    ))
        ->assertSuccessful();
});

test('preview endpoint returns a populated schema for an application', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    asSuperAdmin();

    $application = Application::factory()->create();

    expect($application->fields()->count())->toBeGreaterThan(0);

    $response = get(route('applications.api.preview', ['application' => $application]))
        ->assertSuccessful();

    expect($response->json('schema.children'))->toBeArray()->not->toBeEmpty();
});

test('preview renders the application fields', function () {
    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    asSuperAdmin();

    $application = Application::factory()->create();

    $application->fields()->delete();

    $firstName = $application->fields()->create([
        'label' => 'What is your first name?',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    $aboutYou = $application->fields()->create([
        'label' => 'Tell us about yourself',
        'type' => 'text_area',
        'is_required' => false,
        'config' => [],
    ]);

    $block = fn (ApplicationField $field): array => [
        'type' => 'customBlock',
        'attrs' => [
            'config' => [
                'fieldId' => $field->id,
                'label' => $field->label,
                'isRequired' => $field->is_required,
            ],
            'id' => $field->type,
        ],
    ];

    $application->content = [
        'type' => 'doc',
        'content' => [$block($firstName), $block($aboutYou)],
    ];

    $application->save();

    get(route('applications.api.preview', ['application' => $application]))
        ->assertSuccessful()
        ->assertJsonFragment(['label' => 'What is your first name?'])
        ->assertJsonFragment(['label' => 'Tell us about yourself']);
});

test('authenticate locks out after too many invalid code attempts', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();

    $application = Application::factory()->create();

    $code = '123456';

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'code' => Hash::make($code),
    ]);

    $invalidUrl = URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authentication, 'code' => '654321'],
    );

    for ($attempt = 0; $attempt < AuthenticationCodeRateLimiter::MAX_ATTEMPTS; $attempt++) {
        postJson($invalidUrl)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
    }

    // Once locked out, even the correct code must be rejected.
    postJson(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authentication, 'code' => $code],
    ))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'Too many invalid attempts. Please request a new code.']);
});

test('authenticate resets the attempt counter after a successful authentication', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();

    $application = Application::factory()->create();

    $code = '123456';

    $authentication = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'code' => Hash::make($code),
    ]);

    $invalidUrl = URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authentication, 'code' => '654321'],
    );

    foreach (range(1, AuthenticationCodeRateLimiter::MAX_ATTEMPTS - 1) as $attempt) {
        postJson($invalidUrl)->assertStatus(422);
    }

    postJson(URL::signedRoute(
        name: 'widgets.applications.api.authenticate',
        parameters: ['application' => $application, 'authentication' => $authentication, 'code' => $code],
    ))->assertSuccessful();

    // The successful attempt cleared the counter, so a subsequent wrong code is treated as a fresh attempt rather than a lockout.
    postJson($invalidUrl)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
});

test('request-authentication throttles repeated code requests for the same target', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();

    $application = Application::factory()->create();
    $prospect = Prospect::factory()->create();
    $email = $prospect->primaryEmailAddress->address;

    postJson(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $email],
    ))->assertSuccessful();

    postJson(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $email],
    ))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('request-authentication invalidates prior codes for the same target', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    seed(ApplicationSubmissionStateSeeder::class);

    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineAdmissions = true;
    $settings->save();

    $application = Application::factory()->create();
    $prospect = Prospect::factory()->create();
    $email = $prospect->primaryEmailAddress->address;

    postJson(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $email],
    ))->assertSuccessful();

    $firstId = ApplicationAuthentication::query()
        ->whereMorphedTo('author', $prospect)
        ->where('application_id', $application->id)
        ->value('id');

    expect($firstId)->not->toBeNull();

    // Clear the per-target mint cooldown so a second request is allowed.
    RateLimiter::clear(app(AuthenticationCodeRateLimiter::class)->codeRequestKey($prospect, 'application:' . $application->id));

    postJson(URL::signedRoute(
        name: 'widgets.applications.api.request-authentication',
        parameters: ['application' => $application, 'email' => $email],
    ))->assertSuccessful();

    $records = ApplicationAuthentication::query()
        ->whereMorphedTo('author', $prospect)
        ->where('application_id', $application->id)
        ->get();

    expect($records)->toHaveCount(1);
    expect($records->first()->id)->not->toBe($firstId);
});
