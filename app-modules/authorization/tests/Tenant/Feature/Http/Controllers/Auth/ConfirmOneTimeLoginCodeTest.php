<?php

use AdvisingApp\Authorization\Actions\GenerateOtpLoginCode;
use AdvisingApp\Authorization\Filament\Pages\Auth\ConfirmOneTimeLoginCode;
use AdvisingApp\Authorization\Models\OtpLoginCode;
use App\Models\User;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Livewire\livewire;

function startOneTimeLoginFor(User $user): void
{
    session()->put(ConfirmOneTimeLoginCode::SESSION_KEY, ['user' => $user->getKey()]);
}

it('redirects to login when no login is in progress', function () {
    livewire(ConfirmOneTimeLoginCode::class)
        ->assertRedirect(route('filament.admin.auth.login'));
});

it('authenticates an internal user and sends them to set a password', function () {
    $user = User::factory()->create(['password' => null]);
    $code = app(GenerateOtpLoginCode::class)($user, now()->addDay());
    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect(route('filament.admin.auth.set-password'));

    assertAuthenticatedAs($user);
});

it('rejects an invalid code', function () {
    $user = User::factory()->create(['password' => null]);
    app(GenerateOtpLoginCode::class)($user, now()->addDay());
    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => '000000'])
        ->call('authenticate')
        ->assertNotified('Invalid code')
        ->assertNoRedirect();

    assertGuest();
});

it('marks the code used so it cannot be reused', function () {
    $user = User::factory()->create(['password' => null]);
    $code = app(GenerateOtpLoginCode::class)($user, now()->addDay());
    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate');

    expect(OtpLoginCode::whereNull('used_at')->count())->toBe(0);
});

it('regenerates the session to prevent fixation', function () {
    $user = User::factory()->create(['password' => null]);
    $code = app(GenerateOtpLoginCode::class)($user, now()->addDay());
    startOneTimeLoginFor($user);
    $previous = session()->getId();

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate');

    expect(session()->getId())->not->toBe($previous);
});
