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

use AdvisingApp\Authorization\Filament\Resources\Roles\Pages\CreateRole;
use AdvisingApp\Authorization\Models\Role;
use App\Enums\Feature;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Livewire\Livewire;

use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

test('CreateRole does not allow duplicate role names case insensitively within a guard', function () {
    asSuperAdmin();

    Role::factory()->create(['name' => 'Support Team', 'guard_name' => 'web']);

    // The same name under a different guard is allowed.
    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'support team', 'guard_name' => 'api'])
        ->call('create')
        ->assertHasNoFormErrors();

    // A case-insensitive duplicate under the same guard is rejected.
    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'SUPPORT team', 'guard_name' => 'web'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

describe('enterprise ai', function () {
    it('hides the Enterprise AI permission groups while Enterprise AI is disabled', function () {
        asSuperAdmin();

        $getAvailablePermissionGroupNames = function (): array {
            $availablePermissionGroupNames = [];

            Livewire::test(CreateRole::class)
                ->fillForm(['guard_name' => 'web'])
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
});
