<?php

use App\Models\User;

use function Pest\Laravel\get;

use Illuminate\Support\Facades\URL;

use function Pest\Laravel\assertGuest;
use function Pest\Laravel\assertAuthenticatedAs;

it('signs the user in through a signed URL', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    assertGuest();

    get(URL::signedRoute('login.one-time', ['user' => $user]))
        ->assertRedirect();

    assertAuthenticatedAs($user);
});

it('does not sign the user in if the URL is not signed', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    get(route('login.one-time', ['user' => $user]))
        ->assertForbidden();

    assertGuest();
});

it('does not sign the user in if they have a password set', function () {
    $user = User::factory()->create();

    get(route('login.one-time', ['user' => $user]))
        ->assertForbidden();

    assertGuest();
});

it('does not sign the user in if they are external', function () {
    $user = User::factory()->external()->create();

    get(route('login.one-time', ['user' => $user]))
        ->assertForbidden();

    assertGuest();
});
