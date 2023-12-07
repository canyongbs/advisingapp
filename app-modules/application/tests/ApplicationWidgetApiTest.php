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
