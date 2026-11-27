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

use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\CreateUserGroup;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\UserGroupResource;
use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\Authorization\Tests\Tenant\Filament\Resources\UserGroups\RequestFactories\UserGroupRequestFactory;
use App\Features\UserGroupsFeature;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create user group page', function (): void {
    asSuperAdmin();

    get(UserGroupResource::getUrl('create'))->assertSuccessful();
});

it('can create a user group', function (): void {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $request = UserGroupRequestFactory::new()->create();

    assertDatabaseMissing(UserGroup::class, ['name' => $request['name']]);

    Livewire::test(CreateUserGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $userGroup = UserGroup::query()->where('name', $request['name'])->firstOrFail();

    expect($userGroup->description)->toBe($request['description'])
        ->and($userGroup->createdBy?->is($user))->toBeTrue();
});

it('allows reusing the name of a deleted user group', function (): void {
    asSuperAdmin();

    $deletedUserGroup = UserGroup::factory()->create(['name' => 'Advisors']);
    $deletedUserGroup->delete();

    Livewire::test(CreateUserGroup::class)
        ->fillForm(UserGroupRequestFactory::new()->state(['name' => 'Advisors'])->create())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(UserGroup::class, ['name' => 'Advisors', 'deleted_at' => null]);
});

it('validates the inputs', function (UserGroupRequestFactory $request, array $errors): void {
    asSuperAdmin();

    UserGroup::factory()->create(['name' => 'Advisors']);

    Livewire::test(CreateUserGroup::class)
        ->fillForm($request->create())
        ->call('create')
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
        UserGroupRequestFactory::new()->state(['name' => 'advisors']),
        ['name'],
    ],
    'description max' => fn (): array => [
        UserGroupRequestFactory::new()->state(['description' => str_repeat('a', 65536)]),
        ['description' => 'max'],
    ],
]);

describe('authorization', function (): void {
    it('denies access to the create user group page without the `user_group.create` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any'));

        get(UserGroupResource::getUrl('create'))->assertForbidden();
    });

    it('allows access to the create user group page with the `user_group.create` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.create'));

        get(UserGroupResource::getUrl('create'))->assertSuccessful();
    });

    it('denies access to the create user group page when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        get(UserGroupResource::getUrl('create'))->assertForbidden();
    });
});
