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

use AdvisingApp\Authorization\Filament\Resources\Roles\Pages\EditRole;
use AdvisingApp\Authorization\Models\Role;
use App\Enums\Feature;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Livewire\Livewire;

use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

test('EditRole does not allow duplicate role names case insensitively within a guard', function () {
    asSuperAdmin();

    $role = Role::factory()->create(['name' => 'First Role', 'guard_name' => 'web']);
    Role::factory()->create(['name' => 'Second Role', 'guard_name' => 'web']);

    // Editing a role to its own name in a different case is allowed (record is ignored).
    Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
        ->fillForm(['name' => 'first role'])
        ->call('save')
        ->assertHasNoFormErrors();

    // Colliding with another role's name case-insensitively is rejected.
    Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
        ->fillForm(['name' => 'SECOND role'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

describe('enterprise ai', function () {
    it('hides the Enterprise AI permission groups while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);

        $getAvailablePermissionGroupNames = function () use ($role): array {
            $availablePermissionGroupNames = [];

            Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
                ->assertFormFieldExists('permissions', function (PermissionsMatrix $field) use (&$availablePermissionGroupNames): bool {
                    $availablePermissionGroupNames = array_keys($field->getAvailablePermissions());

                    return true;
                });

            return $availablePermissionGroupNames;
        };

        expect(array_values(array_intersect($getAvailablePermissionGroupNames(), Feature::EnterpriseAi->getPermissionGroupNames())))
            ->toEqualCanonicalizing(Feature::EnterpriseAi->getPermissionGroupNames());

        setEnterpriseAiEnabled(false);

        $availablePermissionGroupNames = $getAvailablePermissionGroupNames();

        expect(array_intersect($availablePermissionGroupNames, Feature::EnterpriseAi->getPermissionGroupNames()))->toBeEmpty()
            ->and($availablePermissionGroupNames)->toContain('User', 'Role');
    });

    it('keeps a role\'s hidden Enterprise AI permissions when it is saved while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $role = Role::factory()->create(['name' => 'Advisors', 'guard_name' => 'web']);
        $role->givePermissionTo(['prompt.view-any', 'user.view-any']);

        setEnterpriseAiEnabled(false);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['name' => 'Renamed Advisors'])
            ->call('save')
            ->assertHasNoFormErrors();

        $role->refresh();

        expect($role->name)->toBe('Renamed Advisors')
            ->and($role->hasPermissionTo('prompt.view-any'))->toBeTrue()
            ->and($role->hasPermissionTo('user.view-any'))->toBeTrue();
    });
});
