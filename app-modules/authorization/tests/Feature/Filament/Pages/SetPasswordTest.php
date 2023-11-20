<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use Illuminate\Support\Str;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertGuest;

use Assist\Authorization\Filament\Pages\Auth\SetPassword;

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
