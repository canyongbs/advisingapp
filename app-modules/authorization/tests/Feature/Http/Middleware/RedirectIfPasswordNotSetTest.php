<?php

use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

use Illuminate\Support\Facades\Hash;

it('redirects if password not set', function () {
    $user = User::factory()->create([
        'password' => null,
    ]);

    actingAs($user);

    get(Filament::getUrl())
        ->assertRedirect(route('filament.admin.auth.set-password'));
});

it('does not redirect if user is external', function () {
    $user = User::factory()->external()->create([
        'password' => null,
    ]);

    actingAs($user);

    get(Filament::getUrl())
        ->assertOk();
});

it('does not redirect if password set', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    get(Filament::getUrl())
        ->assertOk();
});
