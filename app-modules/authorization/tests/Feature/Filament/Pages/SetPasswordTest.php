<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Filament\Pages\Auth\SetPassword;
use App\Models\User;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('sets the user\'s password', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    livewire(SetPassword::class)
        ->fillForm([
            'password' => $password = Str::random(),
            'passwordConfirmation' => $password,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect();

    assertGuest();

    expect($user)
        ->password->not->toBeNull();

    expect(auth()->attempt([
        'email' => $user->email,
        'password' => $password,
    ]))->toBeTrue();
});

it('redirects if the user already has a password', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(SetPassword::class)
        ->assertRedirect();
});

it('redirects if the user is external', function () {
    $user = User::factory()->external()->create();

    actingAs($user);

    livewire(SetPassword::class)
        ->assertRedirect();
});

test('`password` is required', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    livewire(SetPassword::class)
        ->fillForm([
            'password' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['password' => 'required'])
        ->assertNoRedirect();
});

test('`passwordConfirmation` is required', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    livewire(SetPassword::class)
        ->fillForm([
            'passwordConfirmation' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['passwordConfirmation' => 'required'])
        ->assertNoRedirect();
});

test('`password` is the same as `passwordConfirmation`', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    livewire(SetPassword::class)
        ->fillForm([
            'password' => Str::random(),
            'passwordConfirmation' => Str::random(),
        ])
        ->call('save')
        ->assertHasFormErrors(['password' => 'same'])
        ->assertNoRedirect();
});

it('does not redirect if the password is not set', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    get(route('filament.admin.auth.set-password'))
        ->assertOk();
});
