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

use AdvisingApp\Authorization\Filament\Pages\Auth\EmailFirstLogin;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use AdvisingApp\Authorization\Settings\GoogleSsoSettings;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use AdvisingApp\MultifactorAuthentication\Settings\MultifactorSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\mock;

it('starts on the email step', function (): void {
    Livewire::test(EmailFirstLogin::class)
        ->assertSet('step', 'email');
});

it('validates the email on the email step', function (string $email, string $rule): void {
    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $email)
        ->call('continue')
        ->assertHasFormErrors(['email' => $rule])
        ->assertSet('step', 'email');
})->with([
    'email required' => ['', 'required'],
    'email email' => ['not-an-email', 'email'],
]);

it('rate limits repeated calls to continue', function (): void {
    User::factory()->create(['email' => 'user@example.com']);

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', 'user@example.com');

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $component->call('continue')->assertNotNotified();
    }

    $component->call('continue')->assertNotified('Too many login attempts');
});

it('rate limits repeated calls to authenticate', function (): void {
    $user = User::factory()->create();

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'not-the-password');

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $component->call('authenticate')->assertNotNotified();
    }

    $component->call('authenticate')->assertNotified('Too many login attempts');
});

it('advances to the password step for a standard local user and logs them in', function (): void {
    User::factory()->create(['email' => 'user@example.com']);

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', 'user@example.com')
        ->call('continue')
        ->assertSet('step', 'password');

    assertGuest();

    $component
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors();

    assertAuthenticated();
});

it('advances to the password step for an unknown email without revealing it is unknown', function (): void {
    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', 'nobody@example.com')
        ->call('continue')
        ->assertSet('step', 'password')
        ->assertNotNotified();

    assertGuest();

    // A wrong/unknown credential fails with the same generic error as any bad password — no notification,
    // no hint that the account doesn't exist — and the user stays unauthenticated.
    $component
        ->set('data.password', 'not-the-password')
        ->call('authenticate')
        ->assertHasErrors('data.password')
        ->assertNotNotified();

    assertGuest();
});

it('returns to the email step when choosing to use a different email', function (): void {
    User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', 'user@example.com')
        ->call('continue')
        ->assertSet('step', 'password')
        ->set('data.password', 'password')
        ->call('backToEmailStep')
        ->assertSet('data.password', null)
        ->assertSet('data.remember', true)
        ->assertSet('step', 'email');
});

it('shows the generic failure on the password field for an incorrect password', function (): void {
    $user = User::factory()->create();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'not-the-password')
        ->call('authenticate')
        ->assertHasErrors('data.password');

    assertGuest();
});

it('checks remember me by default', function (): void {
    Livewire::test(EmailFirstLogin::class)
        ->assertSet('data.remember', true);
});

it('only shows remember me on the password step', function (): void {
    Livewire::test(EmailFirstLogin::class)
        ->assertSchemaComponentHidden('remember')
        ->set('data.email', 'user@example.com')
        ->call('continue')
        ->assertSchemaComponentVisible('remember');
});

it('remembers the user when remember me is left checked', function (): void {
    $user = User::factory()->create();

    $guard = Filament::auth();
    assert($guard instanceof SessionGuard);

    expect(Cookie::queued($guard->getRecallerName()))->toBeNull();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Cookie::queued($guard->getRecallerName()))->not->toBeNull();
});

it('does not remember the user when remember me is unchecked', function (): void {
    $user = User::factory()->create();

    $guard = Filament::auth();
    assert($guard instanceof SessionGuard);

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->set('data.remember', false)
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Cookie::queued($guard->getRecallerName()))->toBeNull();
});

it('requires multifactor setup when it is required and the user has not set it up', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $user = User::factory()->create();

    expect($user->hasEnabledMultifactor())->toBeFalse();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertSet('needsMfaSetup', true)
        ->assertSet('needsMfa', true);

    assertGuest();

    expect($user->refresh()->hasEnabledMultifactor())->toBeTrue();
});

it('shows the recovery codes after confirming multifactor setup and then logs the user in', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $user = User::factory()->create();

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate');

    $code = app(MultifactorService::class)->getEngine()->getCurrentOtp(decrypt($user->refresh()->multifactor_secret));

    $component
        ->set('data.code', $code)
        ->call('authenticate')
        ->assertActionMounted('recoveryCodes');

    assertGuest();

    expect($user->refresh()->hasConfirmedMultifactor())->toBeTrue();

    $component->callMountedAction();

    assertAuthenticatedAs($user);
});

it('challenges a user with confirmed multifactor and logs them in with a valid code', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    $code = app(MultifactorService::class)->getEngine()->getCurrentOtp(decrypt($user->multifactor_secret));

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertSet('needsMfa', true)
        ->assertSet('needsMfaSetup', false);

    assertGuest();

    $component
        ->set('data.code', $code)
        ->call('authenticate')
        ->assertHasNoFormErrors();

    assertAuthenticatedAs($user);
});

it('passes a code with a leading zero to multifactor verification unchanged', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    mock(MultifactorService::class)
        ->shouldReceive('verify')
        ->once()
        ->with(Mockery::mustBe('012345'), Mockery::type(User::class))
        ->andReturnTrue();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.code', '012345')
        ->call('authenticate')
        ->assertHasNoFormErrors();

    assertAuthenticatedAs($user);
});

it('keeps the user on the multifactor step when the code is invalid', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    $invalidCode = '12345'; // 5 digit code can never be valid

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.code', $invalidCode)
        ->call('authenticate')
        ->assertHasErrors('data.code')
        ->assertSet('needsMfa', true)
        ->assertSet('data.code', null);

    assertGuest();
});

it('keeps the multifactor setup step usable after an invalid submission', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $user = User::factory()->create();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSet('needsMfaSetup', true)
        ->set('data.code', '')
        ->call('authenticate')
        ->assertHasErrors('data.code')
        ->assertSet('needsMfaSetup', true);

    assertGuest();
});

it('logs a user in with a recovery code and consumes it', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    $recoveryCode = $user->multifactor_recovery_codes[0];

    expect($user->multifactor_recovery_codes)->toHaveCount(8);

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->call('toggleUsingRecoveryCodes')
        ->set('data.code', $recoveryCode)
        ->call('authenticate')
        ->assertHasNoFormErrors();

    assertAuthenticatedAs($user);

    expect($user->refresh()->multifactor_recovery_codes)
        ->toHaveCount(7)
        ->not->toContain($recoveryCode);
});

it('only shows the code field on the multifactor step', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->assertSchemaComponentHidden('code')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSchemaComponentVisible('code')
        ->assertSchemaComponentHidden('password')
        ->assertSchemaComponentHidden('remember');
});

it('remembers the user after the multifactor challenge when remember me is left checked', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    $code = app(MultifactorService::class)->getEngine()->getCurrentOtp(decrypt($user->multifactor_secret));

    $guard = Filament::auth();
    assert($guard instanceof SessionGuard);

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->set('data.code', $code)
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Cookie::queued($guard->getRecallerName()))->not->toBeNull();
});

it('remembers the user after the recovery codes modal when remember me is left checked', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $user = User::factory()->create();

    $guard = Filament::auth();
    assert($guard instanceof SessionGuard);

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate');

    $code = app(MultifactorService::class)->getEngine()->getCurrentOtp(decrypt($user->refresh()->multifactor_secret));

    $component
        ->set('data.code', $code)
        ->call('authenticate')
        ->callMountedAction();

    expect(Cookie::queued($guard->getRecallerName()))->not->toBeNull();
});

it('does not show recovery codes before the multifactor code has been verified', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSet('needsMfa', true)
        ->assertActionHidden('recoveryCodes')
        ->mountAction('recoveryCodes')
        ->assertActionNotMounted('recoveryCodes');

    assertGuest();
});

it('requires multifactor setup for an unenrolled user even when a code is submitted', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $user = User::factory()->create();

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSet('needsMfaSetup', true)
        ->set('data.code', '123456');

    $user->refresh()->disableMultifactorAuthentication();

    $component
        ->call('authenticate')
        ->assertSet('needsMfaSetup', true);

    assertGuest();

    expect($user->refresh()->hasEnabledMultifactor())->toBeTrue();
});

it('does not let the browser request the multifactor qr code', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();
    $user->confirmMultifactorAuthentication();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSet('needsMfa', true)
        ->call('getMultifactorQrCode');
})->throws(MethodNotFoundException::class);

it('does not allow switching to another account during a pending multifactor step', function (): void {
    $settings = app(MultifactorSettings::class);
    $settings->required = true;
    $settings->save();

    $unenrolledUser = User::factory()->create();

    $confirmedUser = User::factory()->create();
    $confirmedUser->enableMultifactorAuthentication();
    $confirmedUser->confirmMultifactorAuthentication();

    $invalidCode = '12345'; // 5 digit code can never be valid

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $unenrolledUser->email)
        ->call('continue')
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertSet('needsMfaSetup', true)
        ->set('data.email', $confirmedUser->email)
        ->set('data.code', $invalidCode)
        ->call('authenticate')
        ->assertHasErrors('data.password');

    expect($component->get('user')->is($unenrolledUser))->toBeTrue();

    assertGuest();
});

it('routes an external user based on the available single sign-on providers', function (bool $isAzureEnabled, bool $isGoogleEnabled, Closure $assertRouting): void {
    $azureSettings = app(AzureSsoSettings::class);
    $azureSettings->is_enabled = $isAzureEnabled;
    $azureSettings->client_id = 'azure-client-id';
    $azureSettings->save();

    $googleSettings = app(GoogleSsoSettings::class);
    $googleSettings->is_enabled = $isGoogleEnabled;
    $googleSettings->client_id = 'google-client-id';
    $googleSettings->save();

    $user = User::factory()->external()->create();

    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue');

    $assertRouting($component, $user);

    assertGuest();
})->with([
    'azure only redirects to azure' => [
        true,
        false,
        fn (Testable $component, User $user) => $component
            ->assertRedirect(route('socialite.redirect', ['provider' => 'azure', 'login_hint' => $user->email])),
    ],
    'google only redirects to google' => [
        false,
        true,
        fn (Testable $component, User $user) => $component
            ->assertRedirect(route('socialite.redirect', ['provider' => 'google', 'login_hint' => $user->email])),
    ],
    'azure and google offers a choice' => [
        true,
        true,
        fn (Testable $component, User $user) => $component
            ->assertNoRedirect()
            ->assertSet('step', 'sso')
            ->assertSchemaComponentHidden('remember')
            ->assertActionVisible('azureSso')
            ->assertActionHasUrl('azureSso', route('socialite.redirect', ['provider' => 'azure', 'login_hint' => $user->email]))
            ->assertActionVisible('googleSso')
            ->assertActionHasUrl('googleSso', route('socialite.redirect', ['provider' => 'google', 'login_hint' => $user->email])),
    ],
    'no provider says single sign-on is unavailable' => [
        false,
        false,
        fn (Testable $component) => $component
            ->assertNoRedirect()
            ->assertSet('step', 'email')
            ->assertNotified('Single sign-on is unavailable'),
    ],
]);

it('redirects an external user in the mobile app to azure when google is also available', function (): void {
    $azureSettings = app(AzureSsoSettings::class);
    $azureSettings->is_enabled = true;
    $azureSettings->client_id = 'azure-client-id';
    $azureSettings->save();

    $googleSettings = app(GoogleSsoSettings::class);
    $googleSettings->is_enabled = true;
    $googleSettings->client_id = 'google-client-id';
    $googleSettings->save();

    $user = User::factory()->external()->create();

    app()->rebinding('request', function (Application $app, Request $request): void {
        $request->headers->set('User-Agent', EmailFirstLogin::MOBILE_APP_USER_AGENT_TOKEN);
    });

    Livewire::withHeaders(['User-Agent' => EmailFirstLogin::MOBILE_APP_USER_AGENT_TOKEN])
        ->test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->assertRedirect(route('socialite.redirect', ['provider' => 'azure', 'login_hint' => $user->email]));
});

it('tells an external user in the mobile app that google sign-on is unavailable when it is the only provider', function (): void {
    $googleSettings = app(GoogleSsoSettings::class);
    $googleSettings->is_enabled = true;
    $googleSettings->client_id = 'google-client-id';
    $googleSettings->save();

    $user = User::factory()->external()->create();

    app()->rebinding('request', function (Application $app, Request $request): void {
        $request->headers->set('User-Agent', EmailFirstLogin::MOBILE_APP_USER_AGENT_TOKEN);
    });

    Livewire::withHeaders(['User-Agent' => EmailFirstLogin::MOBILE_APP_USER_AGENT_TOKEN])
        ->test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->assertNoRedirect()
        ->assertSet('step', 'email')
        ->assertSet('googleSsoUnavailableInMobileApp', true);
});

it('matches an external user by email regardless of case and hints the stored email', function (): void {
    $azureSettings = app(AzureSsoSettings::class);
    $azureSettings->is_enabled = true;
    $azureSettings->client_id = 'azure-client-id';
    $azureSettings->save();

    $user = User::factory()->external()->create();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', str($user->email)->upper()->toString())
        ->call('continue')
        ->assertRedirect(route('socialite.redirect', ['provider' => 'azure', 'login_hint' => $user->email]));
});

it('does not let an external user sign in with a password', function (): void {
    $component = Livewire::test(EmailFirstLogin::class)
        ->set('data.email', 'external@example.com')
        ->call('continue')
        ->assertSet('step', 'password');

    User::factory()->external()->create(['email' => 'external@example.com']);

    $component
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasErrors('data.password');

    assertGuest();
});

it('returns to the email step from the single sign-on choice when choosing to use a different email', function (): void {
    $azureSettings = app(AzureSsoSettings::class);
    $azureSettings->is_enabled = true;
    $azureSettings->client_id = 'azure-client-id';
    $azureSettings->save();

    $googleSettings = app(GoogleSsoSettings::class);
    $googleSettings->is_enabled = true;
    $googleSettings->client_id = 'google-client-id';
    $googleSettings->save();

    $user = User::factory()->external()->create();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->assertSet('step', 'sso')
        ->call('backToEmailStep')
        ->assertSet('step', 'email');
});

it('still sends a local user to the password step when single sign-on is available', function (): void {
    $azureSettings = app(AzureSsoSettings::class);
    $azureSettings->is_enabled = true;
    $azureSettings->client_id = 'azure-client-id';
    $azureSettings->save();

    $googleSettings = app(GoogleSsoSettings::class);
    $googleSettings->is_enabled = true;
    $googleSettings->client_id = 'google-client-id';
    $googleSettings->save();

    $user = User::factory()->create();

    Livewire::test(EmailFirstLogin::class)
        ->set('data.email', $user->email)
        ->call('continue')
        ->assertNoRedirect()
        ->assertSet('step', 'password');
});
