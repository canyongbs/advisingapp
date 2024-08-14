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
use AdvisingApp\Team\Models\Team;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;
use AdvisingApp\Team\Filament\Resources\TeamResource;
use AdvisingApp\Team\Filament\Resources\TeamResource\Pages\EditTeam;
use AdvisingApp\Team\Filament\Resources\TeamResource\RelationManagers\UsersRelationManager;

// Permission Tests

test('EditTeam is gated with proper access control', function () {
    $user = User::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertForbidden();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var Team $request */
    $request = Team::factory()->make();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $team->refresh();

    expect($team->name)->toEqual($request->name)
        ->and($team->description)->toEqual($request->description);
});

// Non Super Admin Users can be added to a team test

test('Non Super Admin Users can be added to a team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->has(User::factory()->count(1))->create();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $team,
        'pageClass' => EditTeam::class,
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $user->getKey()]
        )->assertSuccessful();
});

// Super Admin Users cannot be added to a team

test('Super Admin Users cannot be added to a team', function () {
    $user = User::factory()->create();
    $superAdmin = User::factory()->create();
    $team = Team::factory()->create();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    $superAdmin->assignRole('SaaS Global Admin');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $team,
        'pageClass' => EditTeam::class,
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $superAdmin->getKey()]
        )->assertHasTableActionErrors(['recordId'])
        ->assertSeeText('Super admin users cannot be added to a team.');
});

//Super Admin Users do not show up in UsersRelationManager for Teams search results

test('Super Admin Users do not show up in UsersRelationManager for Teams search results', function () {
    $user = User::factory()->create();
    $superAdmin = User::factory()->create();
    $team = Team::factory()->create();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    $superAdmin->assignRole('SaaS Global Admin');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $team,
        'pageClass' => EditTeam::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($superAdmin) {
            $options = $select->getSearchResults($superAdmin->name);

            return empty($options) ? true : false;
        })->assertSuccessful();
});
