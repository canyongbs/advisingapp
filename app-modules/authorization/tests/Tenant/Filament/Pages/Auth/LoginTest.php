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

use AdvisingApp\Authorization\Filament\Pages\Auth\Login;
use AdvisingApp\Authorization\Settings\GoogleSsoSettings;

use function Pest\Laravel\get;

function enableGoogleSso(): void
{
    $googleSsoSettings = app(GoogleSsoSettings::class);
    $googleSsoSettings->is_enabled = true;
    $googleSsoSettings->client_id = 'test-client-id';
    $googleSsoSettings->client_secret = 'test-client-secret';
    $googleSsoSettings->save();
}

it('does not show the switch tenant control or a google unavailable message for a normal browser', function () {
    enableGoogleSso();

    get(route('filament.admin.auth.login'), ['User-Agent' => 'Mozilla/5.0'])
        ->assertOk()
        ->assertDontSee('Switch tenant')
        ->assertDontSee("Google sign-in isn't available in the mobile app")
        ->assertSee('Google');
});

it('shows the switch tenant control for the mobile app', function () {
    get(route('filament.admin.auth.login'), ['User-Agent' => 'AdvisingAppMobile/1.0'])
        ->assertOk()
        ->assertSee('Switch tenant')
        ->assertSee(Login::SWITCH_TENANT_URL);
});

it('does not show the switch tenant control when google sso is not enabled and the user agent is not the mobile app', function () {
    get(route('filament.admin.auth.login'), ['User-Agent' => 'Mozilla/5.0'])
        ->assertOk()
        ->assertDontSee('Switch tenant');
});

it('replaces the google sign-in button with an unavailable message for the mobile app when google sso is enabled', function () {
    enableGoogleSso();

    get(route('filament.admin.auth.login'), ['User-Agent' => 'AdvisingAppMobile/1.0'])
        ->assertOk()
        ->assertSee("Google sign-in isn't available in the mobile app", false)
        ->assertDontSee(route('socialite.redirect', ['provider' => 'google']));
});

it('does not show a google unavailable message for the mobile app when google sso is not enabled', function () {
    get(route('filament.admin.auth.login'), ['User-Agent' => 'AdvisingAppMobile/1.0'])
        ->assertOk()
        ->assertDontSee("Google sign-in isn't available in the mobile app");
});
