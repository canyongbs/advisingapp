<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Pages\Actions\Impersonate;
use App\Filament\Resources\UserResource\Pages\ViewUser;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
});

it('does not render impersonate button for super admin users when user is not super admin', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    actingAs($user);

    $component = livewire(ViewUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = livewire(ViewUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('does not render impersonate button for super admin users even if user has permission', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()
        ->create()
        ->givePermissionTo('authorization.impersonate');

    actingAs($user);

    $component = livewire(ViewUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    $component = livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($user->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('allows user with permission to impersonate', function () {
    $first = User::factory()->create();
    $first->givePermissionTo('authorization.impersonate');
    actingAs($first);

    $second = User::factory()->create();

    $component = livewire(ViewUser::class, [
        'record' => $second->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($second->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($second->id);
});
