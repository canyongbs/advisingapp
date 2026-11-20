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
use App\Filament\Resources\Users\Actions\AssignUserGroupsBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('assigns groups to the selected users without removing their existing groups', function (): void {
    asSuperAdmin();

    $existingUserGroup = UserGroup::factory()->create();
    $newUserGroup = UserGroup::factory()->create();

    $users = User::factory()->count(2)->create();
    $users->each(fn (User $user) => $user->userGroups()->attach($existingUserGroup));

    Livewire::test(ListUsers::class)
        ->selectTableRecords($users)
        ->callAction(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk(), data: [
            'replace' => false,
            'userGroups' => [$newUserGroup->getKey()],
        ])
        ->assertHasNoFormErrors()
        ->assertNotified('Assigned groups');

    $users->each(function (User $user) use ($existingUserGroup, $newUserGroup): void {
        expect($user->userGroups()->pluck('user_groups.id')->all())
            ->toEqualCanonicalizing([$existingUserGroup->getKey(), $newUserGroup->getKey()]);
    });
});

it('replaces the existing groups of the selected users', function (): void {
    asSuperAdmin();

    $existingUserGroup = UserGroup::factory()->create();
    $newUserGroup = UserGroup::factory()->create();

    $users = User::factory()->count(2)->create();
    $users->each(fn (User $user) => $user->userGroups()->attach($existingUserGroup));

    Livewire::test(ListUsers::class)
        ->selectTableRecords($users)
        ->callAction(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk(), data: [
            'replace' => true,
            'userGroups' => [$newUserGroup->getKey()],
        ])
        ->assertHasNoFormErrors();

    $users->each(function (User $user) use ($newUserGroup): void {
        expect($user->userGroups()->pluck('user_groups.id')->all())
            ->toBe([$newUserGroup->getKey()]);
    });
});

it('does not assign archived groups', function (): void {
    asSuperAdmin();

    $archivedUserGroup = UserGroup::factory()->archived()->create();

    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->selectTableRecords([$user])
        ->callAction(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk(), data: [
            'replace' => false,
            'userGroups' => [$archivedUserGroup->getKey()],
        ]);

    expect($user->userGroups()->count())->toBe(0);
});

it('excludes archived groups from the group options', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $archivedUserGroup = UserGroup::factory()->archived()->create();

    $user = User::factory()->create();

    $options = [];

    Livewire::test(ListUsers::class)
        ->selectTableRecords([$user])
        ->mountAction(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk())
        ->assertFormFieldExists('userGroups', function (Select $field) use (&$options): bool {
            $options = $field->getOptions();

            return true;
        });

    expect($options)
        ->toHaveKey($userGroup->getKey())
        ->not->toHaveKey($archivedUserGroup->getKey());
});

describe('authorization', function (): void {
    it('hides the action without the `user.*.update` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view'));

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk());
    });

    it('shows the action with the `user.*.update` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update'));

        Livewire::test(ListUsers::class)
            ->assertActionVisible(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk());
    });

    it('hides the action when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make(AssignUserGroupsBulkAction::class)->table()->bulk());
    });
});
