<?php

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
