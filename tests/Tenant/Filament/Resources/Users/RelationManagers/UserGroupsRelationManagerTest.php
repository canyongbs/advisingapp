<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\UserGroupsRelationManager;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('lists the groups of the user', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();
    $userGroup = UserGroup::factory()->create();
    $otherUserGroup = UserGroup::factory()->create();
    $user->userGroups()->attach($userGroup);

    Livewire::test(UserGroupsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertCanSeeTableRecords([$userGroup])
        ->assertCanNotSeeTableRecords([$otherUserGroup]);
});

it('can attach groups to a user', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();
    $userGroups = UserGroup::factory()->count(2)->create();

    expect($user->userGroups()->count())->toBe(0);

    Livewire::test(UserGroupsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $userGroups->modelKeys(),
        ])
        ->assertHasNoFormErrors();

    expect($user->userGroups()->pluck('user_groups.id')->all())
        ->toEqualCanonicalizing($userGroups->modelKeys());
});

it('can detach a group from a user', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();
    $userGroup = UserGroup::factory()->create();
    $user->userGroups()->attach($userGroup);

    Livewire::test(UserGroupsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(DetachAction::class)->table($userGroup));

    expect($user->userGroups()->count())->toBe(0);
});

it('excludes archived groups from the groups that can be attached', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();
    $userGroup = UserGroup::factory()->create();
    $archivedUserGroup = UserGroup::factory()->archived()->create();

    $options = [];

    Livewire::test(UserGroupsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertFormFieldExists('recordId', function (Select $field) use (&$options): bool {
            $options = $field->getOptions();

            return true;
        });

    expect($options)
        ->toHaveKey($userGroup->getKey())
        ->not->toHaveKey($archivedUserGroup->getKey());
});

describe('authorization', function (): void {
    it('requires the `user_group.view-any` permission to view the relation manager', function (): void {
        $user = User::factory()->create();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update'));

        expect(UserGroupsRelationManager::canViewForRecord($user, EditUser::class))->toBeFalse();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update', 'user_group.view-any'));

        expect(UserGroupsRelationManager::canViewForRecord($user, EditUser::class))->toBeTrue();
    });

    it('hides the relation manager when the `UserGroupsFeature` is inactive', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();

        expect(UserGroupsRelationManager::canViewForRecord($user, EditUser::class))->toBeTrue();

        UserGroupsFeature::deactivate();

        expect(UserGroupsRelationManager::canViewForRecord($user, EditUser::class))->toBeFalse();
    });
});
