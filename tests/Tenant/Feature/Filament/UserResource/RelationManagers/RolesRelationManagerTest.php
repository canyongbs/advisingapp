<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\Role;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('A non-super admin user cannot assign the super admin role.', function () {
    $loggedInUser = User::factory()->licensed(LicenseType::cases())->create();

    $user = User::factory()->create();

    $loggedInUser->givePermissionTo(
        'role.create',
        'role.*.view',
        'role.view-any',
        'role.*.update',
        'user.create',
        'user.*.view',
        'user.view-any',
        'user.*.update',
        'user.*.delete',
        'user.*.restore',
        'user.*.force-delete',
    );

    $loggedInUser->refresh();

    actingAs($loggedInUser);

    $superAdminRole = Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->callTableAction(AttachAction::class, data: ['recordId' => $superAdminRole->getKey()])
        ->assertHasTableActionErrors(['recordId' => 'You are not allowed to select the Super Admin role.']);

    $user->refresh();

    expect($user->roles)->not->toContain($superAdminRole);
});

it('allows user which has sass global admin role to assign sass global admin role to other user', function () {
    $user = User::factory()->create();
    $superAdminRole = Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail();
    $user->assignRole($superAdminRole);

    $secondUser = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $secondUser,
            ])
        )->assertSuccessful();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $secondUser,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return ! empty($options) ? true : false;
        })
        ->callTableAction(AttachAction::class, data: ['recordId' => $superAdminRole->getKey()]);

    $secondUser->refresh();

    expect($secondUser->roles->pluck('id'))->toContain($superAdminRole->getKey());
});

it('does not display the Saas Global Admin role if the user is not itself a Saas Global Admin', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(
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

    $user->refresh();

    $secondUser = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $secondUser,
            ])
        )->assertSuccessful();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $secondUser,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return empty($options) ? true : false;
        })
        ->assertSuccessful();
});
