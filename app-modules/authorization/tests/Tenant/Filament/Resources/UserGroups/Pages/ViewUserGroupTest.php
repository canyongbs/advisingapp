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

use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\ViewUserGroup;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\UserGroupResource;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    UserGroupsFeature::activate();
});

it('can render the view user group page', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    get(UserGroupResource::getUrl('view', ['record' => $userGroup]))->assertSuccessful();
});

it('displays the user group data', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    Livewire::test(ViewUserGroup::class, ['record' => $userGroup->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $userGroup->name,
            'description' => $userGroup->description,
        ]);
});

describe('authorization', function (): void {
    it('denies access to the view user group page without the `user_group.*.view` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any'));

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('view', ['record' => $userGroup]))->assertForbidden();
    });

    it('allows access to the view user group page with the `user_group.*.view` permission', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view'));

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('view', ['record' => $userGroup]))->assertSuccessful();
    });

    it('denies access to the view user group page when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        $userGroup = UserGroup::factory()->create();

        get(UserGroupResource::getUrl('view', ['record' => $userGroup]))->assertForbidden();
    });
});
