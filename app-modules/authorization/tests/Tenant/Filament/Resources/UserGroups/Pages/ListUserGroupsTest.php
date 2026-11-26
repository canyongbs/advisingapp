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

use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\ListUserGroups;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\UserGroupResource;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    UserGroupsFeature::activate();
});

it('can render the list user groups page', function (): void {
    asSuperAdmin();

    get(UserGroupResource::getUrl())->assertSuccessful();
});

it('can list user groups', function (): void {
    asSuperAdmin();

    $userGroups = UserGroup::factory()->count(3)->create();

    Livewire::test(ListUserGroups::class)
        ->assertCanSeeTableRecords($userGroups);
});

it('does not list deleted user groups', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $deletedUserGroup = UserGroup::factory()->create();
    $deletedUserGroup->delete();

    Livewire::test(ListUserGroups::class)
        ->assertCanSeeTableRecords([$userGroup])
        ->assertCanNotSeeTableRecords([$deletedUserGroup]);
});

it('can sort by column', function (string $column): void {
    asSuperAdmin();

    $userGroups = UserGroup::factory()->count(3)->create();

    $userGroups->each(function (UserGroup $userGroup, int $index): void {
        $userGroup->users()->attach(User::factory()->count($index)->create());
    });

    Livewire::test(ListUserGroups::class)
        ->sortTable($column)
        ->assertCanSeeTableRecords($userGroups->sortBy(fn (UserGroup $userGroup) => data_get($userGroup->loadCount('users'), $column)), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($userGroups->sortByDesc(fn (UserGroup $userGroup) => data_get($userGroup->loadCount('users'), $column)), inOrder: true);
})->with([
    'name',
    'users_count',
]);

it('can search by column', function (): void {
    asSuperAdmin();

    $matchingUserGroup = UserGroup::factory()->create(['name' => 'Financial Aid Advisors']);
    $otherUserGroup = UserGroup::factory()->create(['name' => 'Registrar']);

    Livewire::test(ListUserGroups::class)
        ->searchTable('financial')
        ->assertCanSeeTableRecords([$matchingUserGroup])
        ->assertCanNotSeeTableRecords([$otherUserGroup]);
});

it('shows the member count for each user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $userGroup->users()->attach(User::factory()->count(2)->create());

    Livewire::test(ListUserGroups::class)
        ->assertTableColumnStateSet('users_count', 2, record: $userGroup);
});

it('shows a truncated description under the name', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create(['description' => str_repeat('a', 50)]);

    Livewire::test(ListUserGroups::class)
        ->assertTableColumnHasDescription('name', str_repeat('a', 40) . '...', record: $userGroup);
});

describe('deletion', function (): void {
    it('bulk deletes user groups', function (): void {
        asSuperAdmin();

        $userGroups = UserGroup::factory()->count(2)->create();

        Livewire::test(ListUserGroups::class)
            ->selectTableRecords($userGroups)
            ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk());

        $userGroups->each(fn (UserGroup $userGroup) => assertSoftDeleted($userGroup));
    });

    it('hides the `DeleteBulkAction` without the `user_group.*.delete` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any'));

        $userGroup = UserGroup::factory()->create();

        Livewire::test(ListUserGroups::class)
            ->assertActionHidden(TestAction::make(DeleteBulkAction::class)->table()->bulk());

        assertModelExists($userGroup);
    });
});

describe('authorization', function (): void {
    it('denies access to the list user groups page without the `user_group.view-any` permission', function (): void {
        actingAs(User::factory()->create());

        get(UserGroupResource::getUrl())->assertForbidden();
    });

    it('allows access to the list user groups page with the `user_group.view-any` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any'));

        get(UserGroupResource::getUrl())->assertSuccessful();
    });

    it('allows a super admin to access the list user groups page', function (): void {
        asSuperAdmin();

        get(UserGroupResource::getUrl())->assertSuccessful();
    });

    it('denies access to the list user groups page when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        get(UserGroupResource::getUrl())->assertForbidden();
    });
});
