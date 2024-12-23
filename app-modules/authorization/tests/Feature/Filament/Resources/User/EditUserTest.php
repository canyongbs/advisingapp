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

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;
use Illuminate\View\ViewException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Pages\Actions\Impersonate;

use function Tests\asSuperAdmin;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
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
    livewire(EditUser::class, ['record' => $superAdmin->getRouteKey()])
        ->assertStatus(404);
})->throws(ViewException::class);

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = livewire(EditUser::class, [
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

    $component = livewire(EditUser::class, [
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
    $first->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update');
    asSuperAdmin($first);

    $second = User::factory()->create();

    $component = livewire(EditUser::class, [
        'record' => $second->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($second->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($second->id);
});
it('allows user which has sass global admin role to assign sass global admin role to other user', function () {
    $user = User::factory()->create();
    $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $second = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $second,
            ])
        )->assertSuccessful();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $second,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return ! empty($options) ? true : false;
        })->assertSuccessful();
});
it('Not allows user which has not sass global admin role to assign sass global admin role to other user', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(
        'permission.view-any',
        'permission.*.view',
        'role.view-any',
        'role.*.view',
        'user.view-any',
        'user.*.view',
        'user.create',
        'user.*.update',
        'user.*.delete',
        'user.*.restore',
        'user.*.force-delete',
    );

    $second = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $second,
            ])
        )->assertSuccessful();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $second,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return empty($options) ? true : false;
        })->assertSuccessful();
});
