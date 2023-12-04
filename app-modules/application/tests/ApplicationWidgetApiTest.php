<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;

use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\URL;
use Assist\Prospect\Models\Prospect;
use Assist\Application\Models\Application;

use function Pest\Laravel\withoutMiddleware;

use Assist\Application\Models\ApplicationAuthentication;
use Assist\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;

test('define is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    get(URL::signedRoute('applications.define', ['application' => $application]))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    get(URL::signedRoute('applications.define', ['application' => $application]))
        ->assertSuccessful();
});

test('request-authentication is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    $prospect = Prospect::factory()->create();

    post(URL::signedRoute('applications.request-authentication', ['application' => $application, 'email' => $prospect->email]))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute('applications.request-authentication', ['application' => $application, 'email' => $prospect->email]))
        ->assertSuccessful();
});

test('authenticate is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineAdmissions = false;

    $settings->save();

    $application = Application::factory()->create();

    $code = random_int(100000, 999999);

    $authorization = ApplicationAuthentication::factory()->create([
        'application_id' => $application->id,
        'code' => Hash::make($code),
    ]);

    post(URL::signedRoute('applications.authenticate', ['application' => $application, 'authentication' => $authorization,  'code' => $code]))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute('applications.authenticate', ['application' => $application, 'authentication' => $authorization, 'code' => $code]))
        ->assertSuccessful();
});

test('submit is protected with proper feature access control', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

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

    post(URL::signedRoute('applications.submit', ['application' => $application, 'authentication' => $authorization]))
        ->assertForbidden()
        ->assertJson([
            'error' => 'Online Admissions is not enabled.',
        ]);

    $settings->data->addons->onlineAdmissions = true;

    $settings->save();

    post(URL::signedRoute('applications.submit', ['application' => $application, 'authentication' => $authorization]))
        ->assertSuccessful();
});
