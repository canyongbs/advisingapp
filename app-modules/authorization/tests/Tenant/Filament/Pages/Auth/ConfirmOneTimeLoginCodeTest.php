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

use AdvisingApp\Authorization\Actions\GenerateOneTimeLoginCode;
use AdvisingApp\Authorization\Filament\Pages\Auth\ConfirmOneTimeLoginCode;
use AdvisingApp\Authorization\Models\OneTimeLoginCode;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Livewire\livewire;

function startOneTimeLoginFor(User $user): void
{
    session()->put(ConfirmOneTimeLoginCode::SESSION_KEY, [
        'user' => $user->getKey(),
    ]);
}

it('redirects to the login page when no login is in progress', function () {
    livewire(ConfirmOneTimeLoginCode::class)
        ->assertRedirect(route('filament.admin.auth.login'));
});

it('renders when a login is in progress', function () {
    $user = User::factory()->create(['password' => null]);

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->assertOk()
        ->assertNoRedirect();
});

it('authenticates an internal user with a valid code and sends them to set a password', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    startOneTimeLoginFor($user);

    assertGuest();

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect(route('filament.admin.auth.set-password'));

    assertAuthenticatedAs($user);
});

it('lands an external user on the home page', function () {
    $user = User::factory()->external()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addHour());

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertRedirect(Filament::getPanel('admin')->getUrl());

    assertAuthenticatedAs($user);
});

it('soft deletes the code once it has been used so it cannot be reused', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(OneTimeLoginCode::count())->toBe(0)
        ->and(OneTimeLoginCode::withTrashed()->count())->toBe(1)
        ->and(OneTimeLoginCode::withTrashed()->firstOrFail()->trashed())->toBeTrue();
});

it('does not authenticate when the code has already been used', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    OneTimeLoginCode::query()->firstOrFail()->delete();

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertNotified('Invalid code')
        ->assertNoRedirect();

    assertGuest();
});

it('does not authenticate with an expired code', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->subMinute());

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertNotified('Invalid code')
        ->assertNoRedirect();

    assertGuest();
});

it('does not authenticate with an incorrect code', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    $wrongCode = str_pad((string) (((int) $code + 1) % 1000000), 6, '0', STR_PAD_LEFT);

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $wrongCode])
        ->call('authenticate')
        ->assertNotified('Invalid code')
        ->assertNoRedirect();

    assertGuest();

    expect(OneTimeLoginCode::count())->toBe(1);
});

it('does not consume a code belonging to another user', function () {
    $user = User::factory()->create(['password' => null]);
    $otherUser = User::factory()->create(['password' => null]);

    $otherCode = app(GenerateOneTimeLoginCode::class)($otherUser, now()->addDay());

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $otherCode])
        ->call('authenticate')
        ->assertNotified('Invalid code')
        ->assertNoRedirect();

    assertGuest();

    expect(OneTimeLoginCode::count())->toBe(1);
});

it('forgets the login session data after authenticating', function () {
    $user = User::factory()->create(['password' => null]);

    $code = app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(session()->has(ConfirmOneTimeLoginCode::SESSION_KEY))->toBeFalse();
});

it('validates the code', function (?string $code, array $errors) {
    $user = User::factory()->create(['password' => null]);

    startOneTimeLoginFor($user);

    livewire(ConfirmOneTimeLoginCode::class)
        ->fillForm(['code' => $code])
        ->call('authenticate')
        ->assertHasFormErrors($errors)
        ->assertNoRedirect();

    assertGuest();
})->with([
    'code required' => fn () => [null, ['code' => 'required']],
    'code numeric' => fn () => ['abcdef', ['code']],
    'code length' => fn () => ['123', ['code']],
]);

it('rate limits repeated attempts', function () {
    $user = User::factory()->create(['password' => null]);

    app(GenerateOneTimeLoginCode::class)($user, now()->addDay());

    startOneTimeLoginFor($user);

    $component = livewire(ConfirmOneTimeLoginCode::class);

    foreach (range(1, 5) as $attempt) {
        $component
            ->fillForm(['code' => '000000'])
            ->call('authenticate');
    }

    $component
        ->fillForm(['code' => '000000'])
        ->call('authenticate')
        ->assertNotified('Too many attempts')
        ->assertNoRedirect();

    assertGuest();
});
