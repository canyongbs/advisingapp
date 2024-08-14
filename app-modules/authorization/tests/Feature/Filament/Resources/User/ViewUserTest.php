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

use App\Models\User;

use function Tests\asSuperAdmin;

use Illuminate\View\ViewException;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Pest\Laravel\assertDatabaseHas;

use STS\FilamentImpersonate\Pages\Actions\Impersonate;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    livewire(ViewUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('does not render super admin profile for regular user', function () {
    // Create a super admin user
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    // Verify super admin user exists
    assertDatabaseHas('users', ['id' => $superAdmin->id]);

    // Create another user
    $user = User::factory()->create();
    actingAs($user);

    // Attempt to load the EditUser component with the super admin's route key
    livewire(ViewUser::class, ['record' => $superAdmin->getRouteKey()])
        ->assertStatus(404);
})->throws(ViewException::class);

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($user->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('allows user with permission to impersonate', function () {
    $first = User::factory()->create();
    $first->givePermissionTo('user.view-any', 'user.*.view', 'authorization.impersonate');
    actingAs($first);

    $second = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $second->getRouteKey(),
    ])
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($second->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($second->id);
});


it('does not display the mfa_reset Action if the user is external', function () {
    $user = User::factory()->external()->create();

    asSuperAdmin();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertActionHidden('mfa_reset');
});

it('does not display the mfa_reset Action if the authed user does not have the proper permission', function () {
    $user = User::factory()->external()->create();

    $user->enableMultifactorAuthentication();

    $user->confirmMultifactorAuthentication();

    $actingAsUser = User::factory()->create();
    $actingAsUser->givePermissionTo('user.view-any', 'user.*.view');
    actingAs($actingAsUser);

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertActionHidden('mfa_reset');
});

it('does not display the mfa_reset Action if the user is internal but has not enabled and/or confirmed MFA', function () {
    $user = User::factory()->internal()->create();

    asSuperAdmin();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertActionHidden('mfa_reset');
});

it('displays the mfa_reset Action if the user is internal, has MFA enabled and/or confirmed, and the authed user has proper permission', function (User $user) {
    $actingAsUser = User::factory()->create();
    $actingAsUser->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update');
    actingAs($actingAsUser);

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertActionVisible('mfa_reset');
})->with([
    'Has MFA Enabled' => function () {
        return tap(
            User::factory()->internal()->create(),
            function (User $user) {
                $user->enableMultifactorAuthentication();
            }
        );
    },
    'Has MFA Confirmed' => function () {
        return tap(
            User::factory()->internal()->create(),
            function (User $user) {
                $user->enableMultifactorAuthentication();

                $user->confirmMultifactorAuthentication();
            }
        );
    },
]);

it('resets the users MFA when the mfa_reset Action is triggered', function () {
    $user = User::factory()->internal()->create();

    $user->enableMultifactorAuthentication();

    $user->confirmMultifactorAuthentication();

    $user->refresh();

    expect($user->multifactor_confirmed_at)->not()->toBeNull()
        ->and($user->multifactor_secret)->not()->toBeNull()
        ->and($user->multifactor_recovery_codes)->not()->toBeNull();

    asSuperAdmin();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->callAction('mfa_reset');

    $user->refresh();

    expect($user->multifactor_confirmed_at)->toBeNull()
        ->and($user->multifactor_secret)->toBeNull()
        ->and($user->multifactor_recovery_codes)->toBeNull();
});
