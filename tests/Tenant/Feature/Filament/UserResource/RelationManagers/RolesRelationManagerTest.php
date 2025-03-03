<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\Role;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('A non-super admin user cannot assign the super admin role.', function () {
    $loggedInUser = User::factory()->licensed(LicenseType::cases())->create();

    $user = User::factory()->create();

    $loggedInUser->givePermissionTo('role.create');
    $loggedInUser->givePermissionTo('role.*.view');
    $loggedInUser->givePermissionTo('role.view-any');
    $loggedInUser->givePermissionTo('role.*.update');
    $loggedInUser->givePermissionTo('user.create');
    $loggedInUser->givePermissionTo('user.*.view');
    $loggedInUser->givePermissionTo('user.view-any');
    $loggedInUser->givePermissionTo('user.*.update');

    actingAs($loggedInUser);

    $superAdminRole = Role::first();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->callTableAction(AttachAction::class, data: ['recordId' => $superAdminRole->getKey()])
        ->assertHasTableActionErrors(['recordId' => 'The selected role is not allowed.'])
        ->assertSuccessful();
});
