<?php

use AdvisingApp\Authorization\Filament\Pages\Auth\EmailFirstLogin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

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
        ->assertFormFieldHidden('remember')
        ->set('data.email', 'user@example.com')
        ->call('continue')
        ->assertFormFieldVisible('remember');
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
