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

use AdvisingApp\Authorization\Filament\Pages\Auth\SetPassword;
use AdvisingApp\Authorization\Tests\Tenant\Filament\Pages\Auth\RequestFactories\SetPasswordRequestFactory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Livewire\livewire;

it("sets the user's password", function () {
    $user = User::factory()->create(['password' => null]);

    actingAs($user);

    $request = SetPasswordRequestFactory::new()->create();

    livewire(SetPassword::class)
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(route('filament.admin.auth.login'));

    assertGuest();

    expect($user->refresh()->password)->not->toBeNull();

    expect(auth()->attempt([
        'email' => $user->email,
        'password' => $request['password'],
    ]))->toBeTrue();
});

it('regenerates the remember token when the password is set', function () {
    $user = User::factory()->create([
        'password' => null,
        'remember_token' => 'original-token',
    ]);

    actingAs($user);

    livewire(SetPassword::class)
        ->fillForm(SetPasswordRequestFactory::new()->create())
        ->call('save')
        ->assertHasNoFormErrors();

    $rememberToken = $user->refresh()->remember_token;

    expect($rememberToken)->not->toBeNull();
    expect($rememberToken)->not->toBe('original-token');
});

it('validates the inputs', function (SetPasswordRequestFactory $data, array $errors) {
    $user = User::factory()->create(['password' => null]);

    actingAs($user);

    $request = SetPasswordRequestFactory::new($data)->create();

    livewire(SetPassword::class)
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNoRedirect();

    expect($user->refresh()->password)->toBeNull();
})->with([
    'password required' => fn () => [
        SetPasswordRequestFactory::new()->state(['password' => null]),
        ['password' => 'required'],
    ],
    'password must meet the default strength rules' => fn () => [
        SetPasswordRequestFactory::new()->state([
            'password' => 'short',
            'passwordConfirmation' => 'short',
        ]),
        ['password' => Password::class],
    ],
    'passwordConfirmation required' => fn () => [
        SetPasswordRequestFactory::new()->state(['passwordConfirmation' => null]),
        ['passwordConfirmation' => 'required'],
    ],
    'password same as passwordConfirmation' => fn () => [
        SetPasswordRequestFactory::new()->state([
            'password' => Str::random(),
            'passwordConfirmation' => Str::random(),
        ]),
        ['password' => 'same'],
    ],
]);

it('rate limits repeated attempts', function () {
    $user = User::factory()->create(['password' => null]);

    actingAs($user);

    $component = livewire(SetPassword::class)
        ->fillForm(SetPasswordRequestFactory::new()->state(['password' => null])->create());

    $component->call('save')->assertNotNotified();
    $component->call('save')->assertNotNotified();

    $component->call('save')
        ->assertNotified('Too many attempts')
        ->assertNoRedirect();

    expect($user->refresh()->password)->toBeNull();
});

describe('redirects', function () {
    it('redirects if the user already has a password', function () {
        $user = User::factory()->create();

        actingAs($user);

        livewire(SetPassword::class)
            ->assertRedirect();
    });

    it('redirects if the user is external', function () {
        $user = User::factory()->external()->create(['password' => null]);

        actingAs($user);

        livewire(SetPassword::class)
            ->assertRedirect();
    });

    it('does not redirect if the password is not set', function () {
        $user = User::factory()->create(['password' => null]);

        actingAs($user);

        livewire(SetPassword::class)
            ->assertOk()
            ->assertNoRedirect();
    });
});
