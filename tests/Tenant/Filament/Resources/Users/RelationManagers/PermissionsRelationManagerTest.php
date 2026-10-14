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

use AdvisingApp\Authorization\Models\Permission;
use AdvisingApp\Authorization\Models\PermissionGroup;
use AdvisingApp\Authorization\Models\Role;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\PermissionsRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

describe('enterprise ai', function () {
    it('hides Enterprise AI permissions while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $role->givePermissionTo(['prompt.view-any', 'user.view-any']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $aiPermission = Permission::query()->where('name', 'prompt.view-any')->where('guard_name', 'web')->firstOrFail();
        $otherPermission = Permission::query()->where('name', 'user.view-any')->where('guard_name', 'web')->firstOrFail();

        Livewire::test(PermissionsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->assertCanSeeTableRecords([$aiPermission, $otherPermission]);

        setEnterpriseAiEnabled(false);

        Livewire::test(PermissionsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->assertCanSeeTableRecords([$otherPermission])
            ->assertCanNotSeeTableRecords([$aiPermission]);
    });

    it('excludes Enterprise AI permission groups from the group filter while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $user = User::factory()->create();

        $aiPermissionGroup = PermissionGroup::query()->where('name', 'Prompt')->firstOrFail();

        $searchGroupFilterOptions = function (string $search) use ($user): array {
            $results = [];

            Livewire::test(PermissionsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
                ->assertFormFieldExists('group.values', 'tableFiltersForm', function (Select $field) use ($search, &$results): bool {
                    $results = $field->getSearchResults($search);

                    return true;
                });

            return $results;
        };

        expect($searchGroupFilterOptions('Prompt'))->toHaveKey($aiPermissionGroup->getKey());

        setEnterpriseAiEnabled(false);

        expect($searchGroupFilterOptions('Prompt'))->not->toHaveKey($aiPermissionGroup->getKey());
    });
});
