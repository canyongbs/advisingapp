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
