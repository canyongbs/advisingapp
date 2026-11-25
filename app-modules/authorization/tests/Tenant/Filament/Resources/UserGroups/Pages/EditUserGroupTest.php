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
use AdvisingApp\Authorization\Filament\Resources\UserGroups\UserGroupResource;
use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\Authorization\Tests\Tenant\Filament\Resources\UserGroups\RequestFactories\UserGroupRequestFactory;
use App\Features\UserGroupsFeature;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    UserGroupsFeature::activate();
});

it('can render the edit user group page', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    get(UserGroupResource::getUrl('edit', ['record' => $userGroup]))->assertSuccessful();
});

it('displays the user group data', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $userGroup->name,
            'description' => $userGroup->description,
        ]);
});

it('can update a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    $request = UserGroupRequestFactory::new()->create();

    expect($userGroup->name)->not->toBe($request['name']);

    Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($userGroup->refresh())
        ->name->toBe($request['name'])
        ->description->toBe($request['description']);
});

it('allows saving with the same name as the current record', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
        ->fillForm(['name' => $userGroup->name])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('validates the inputs', function (UserGroupRequestFactory $request, array $errors): void {
    asSuperAdmin();

    UserGroup::factory()->create(['name' => 'Advisors']);

    $userGroup = UserGroup::factory()->create();

    Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
        ->fillForm($request->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => fn (): array => [
        UserGroupRequestFactory::new()->state(['name' => null]),
        ['name' => 'required'],
    ],
    'name max' => fn (): array => [
        UserGroupRequestFactory::new()->state(['name' => str_repeat('a', 256)]),
        ['name' => 'max'],
    ],
    'name unique' => fn (): array => [
        UserGroupRequestFactory::new()->state(['name' => 'ADVISORS']),
        ['name'],
    ],
    'description max' => fn (): array => [
        UserGroupRequestFactory::new()->state(['description' => str_repeat('a', 65536)]),
        ['description' => 'max'],
    ],
]);

describe('deletion', function (): void {
    it('can delete a user group', function (): void {
        asSuperAdmin();

        $userGroup = UserGroup::factory()->create();

        Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
            ->callAction(DeleteAction::class);

        assertSoftDeleted($userGroup);
    });

    it('hides the `DeleteAction` without the `user_group.*.delete` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update'));

        $userGroup = UserGroup::factory()->create();

        Livewire::test(EditUserGroup::class, ['record' => $userGroup->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the edit user group page without the `user_group.*.update` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view'));

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('edit', ['record' => $userGroup]))->assertForbidden();
    });

    it('allows access to the edit user group page with the `user_group.*.update` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update'));

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('edit', ['record' => $userGroup]))->assertSuccessful();
    });

    it('denies access to the edit user group page when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('edit', ['record' => $userGroup]))->assertForbidden();
    });
});
