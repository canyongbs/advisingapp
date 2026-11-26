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

use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\EditUserGroup;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\ViewUserGroup;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\RelationManagers\UsersRelationManager;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('can attach users to a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $users = User::factory()->count(2)->create();

    expect($userGroup->users()->count())->toBe(0);

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $users->modelKeys(),
        ])
        ->assertHasNoActionErrors();

    expect($userGroup->users()->pluck('users.id')->all())
        ->toEqualCanonicalizing($users->modelKeys());
});

it('can detach a user from a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $user = User::factory()->create();
    $userGroup->users()->attach($user);

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(DetachAction::class)->table($user));

    expect($userGroup->users()->whereKey($user->getKey())->exists())->toBeFalse();
});

it('bulk detaches users from a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $users = User::factory()->count(2)->create();
    $userGroup->users()->attach($users);

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->selectTableRecords($users)
        ->callAction(TestAction::make(DetachBulkAction::class)->table()->bulk());

    expect($userGroup->users()->count())->toBe(0);
});

it('excludes admins from the users that can be attached when admins are filtered from selection', function (bool $isFilteringAdmins): void {
    config(['app.filter_admins_from_selection' => $isFilteringAdmins]);

    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $userGroup = UserGroup::factory()->create();
    $user = User::factory()->create();

    $options = [];

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertFormFieldExists('recordId', function (Select $field) use (&$options): bool {
            $options = $field->getOptions();

            return true;
        });

    expect($options)->toHaveKey($user->getKey());

    if ($isFilteringAdmins) {
        expect($options)->not->toHaveKey($superAdmin->getKey());
    } else {
        expect($options)->toHaveKey($superAdmin->getKey());
    }
})->with([
    'filtering admins' => true,
    'not filtering admins' => false,
]);

it('hides the `AttachAction` for an archived user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->archived()->create();

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->assertActionHidden(TestAction::make(AttachAction::class)->table());
});

describe('authorization', function (): void {
    it('requires the `user.view-any` permission to view the relation manager', function (): void {
        $userGroup = UserGroup::factory()->create();

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update'));

        expect(UsersRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeFalse();

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update', 'user.view-any'));

        expect(UsersRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeTrue();
    });

    it('hides the attach and detach actions without the `user_group.*.update` permission', function (): void {
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();
        $userGroup->users()->attach($user);

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user.view-any', 'user.*.update'));

        Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => ViewUserGroup::class])
            ->assertActionHidden(TestAction::make(AttachAction::class)->table())
            ->assertActionHidden(TestAction::make(DetachAction::class)->table($user))
            ->assertActionHidden(TestAction::make(DetachBulkAction::class)->table()->bulk());
    });

    it('shows the attach and detach actions with the `user_group.*.update` permission', function (): void {
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();
        $userGroup->users()->attach($user);

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update', 'user.view-any'));

        Livewire::test(UsersRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => ViewUserGroup::class])
            ->assertActionVisible(TestAction::make(AttachAction::class)->table())
            ->assertActionVisible(TestAction::make(DetachAction::class)->table($user))
            ->assertActionVisible(TestAction::make(DetachBulkAction::class)->table()->bulk());
    });

    it('hides the relation manager when the `UserGroupsFeature` is inactive', function (): void {
        asSuperAdmin();

        $userGroup = UserGroup::factory()->create();

        expect(UsersRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeTrue();

        UserGroupsFeature::deactivate();

        expect(UsersRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeFalse();
    });
});
