<?php

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
        'user.*.update'
    );

    actingAs($loggedInUser);

    $superAdminRole = Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->callTableAction(AttachAction::class, data: ['recordId' => $superAdminRole->getKey()])
        ->assertHasTableActionErrors(['recordId' => 'The selected role is not allowed.']);
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
        })
        ->assertSuccessful();
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
        })
        ->assertSuccessful();
});
