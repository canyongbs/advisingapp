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
use AdvisingApp\Authorization\Models\UserGroup;
use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

test('CreateRole does not allow duplicate role names case insensitively within a guard', function (): void {
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

describe('membership', function (): void {
    it('can create a role with users and groups', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $group = UserGroup::factory()->create();

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'Advisors',
                'guard_name' => 'web',
                'users' => [$user->getKey()],
                'userGroups' => [$group->getKey()],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::query()->where('name', 'Advisors')->firstOrFail();

        expect($role->users->modelKeys())->toBe([$user->getKey()])
            ->and($role->userGroups->modelKeys())->toBe([$group->getKey()]);
    });

    it('can create an `api` role with system users', function (): void {
        asSuperAdmin();

        $systemUser = SystemUser::factory()->create();

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'Integrations',
                'guard_name' => 'api',
            ])
            ->fillForm([
                'users' => [$systemUser->getKey()],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::query()->where('name', 'Integrations')->firstOrFail();

        expect($role->users->modelKeys())->toBe([$systemUser->getKey()])
            ->and($role->users->first())->toBeInstanceOf(SystemUser::class);
    });

    it('only offers the users of the selected access control', function (): void {
        asSuperAdmin();

        $nameMatch = User::factory()->create(['name' => 'Jordan Rivers']);
        $emailMatch = User::factory()->create(['name' => 'Casey Brooks', 'email' => 'jordan.brooks@example.com']);
        $nonMatch = User::factory()->create(['name' => 'Taylor Stone']);
        $systemUser = SystemUser::factory()->create(['name' => 'Jordan Integration']);

        Livewire::test(CreateRole::class)
            ->fillForm(['guard_name' => 'web'])
            ->assertFormFieldExists('users', function (Select $field) use ($nameMatch, $emailMatch, $nonMatch, $systemUser): bool {
                $results = $field->getSearchResults('JORDAN');

                expect($results)->toHaveKeys([$nameMatch->getKey(), $emailMatch->getKey()])
                    ->not->toHaveKey($nonMatch->getKey())
                    ->not->toHaveKey($systemUser->getKey());

                return true;
            })
            ->fillForm(['guard_name' => 'api'])
            ->assertFormFieldExists('users', function (Select $field) use ($nameMatch, $emailMatch, $systemUser): bool {
                expect($field->getSearchResults('jordan'))->toBe([$systemUser->getKey() => $systemUser->name])
                    ->and($field->getOptions())->toHaveKey($systemUser->getKey())
                    ->not->toHaveKey($nameMatch->getKey())
                    ->not->toHaveKey($emailMatch->getKey());

                return true;
            });
    });

    it('does not create a role with users of another access control', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'Integrations',
                'guard_name' => 'api',
            ])
            ->fillForm([
                'users' => [$user->getKey()],
            ])
            ->call('create')
            ->assertHasFormErrors(['users.0' => 'in']);

        expect(Role::query()->where('name', 'Integrations')->exists())->toBeFalse();
    });

    it('clears the membership when the access control changes', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $group = UserGroup::factory()->create();

        Livewire::test(CreateRole::class)
            ->fillForm([
                'guard_name' => 'web',
                'users' => [$user->getKey()],
                'userGroups' => [$group->getKey()],
            ])
            ->fillForm([
                'guard_name' => 'api',
            ])
            ->assertSchemaStateSet([
                'users' => [],
                'userGroups' => [],
            ]);
    });

    it('hides the groups for `api` roles', function (): void {
        asSuperAdmin();

        Livewire::test(CreateRole::class)
            ->fillForm(['guard_name' => 'web'])
            ->assertFormFieldVisible('userGroups')
            ->fillForm(['guard_name' => 'api'])
            ->assertFormFieldHidden('userGroups');
    });

    it('does not offer archived groups', function (): void {
        asSuperAdmin();

        $group = UserGroup::factory()->create();
        $archivedGroup = UserGroup::factory()->create();
        $archivedGroup->archive();

        Livewire::test(CreateRole::class)
            ->assertFormFieldExists('userGroups', function (Select $field) use ($group, $archivedGroup): bool {
                expect($field->getOptions())->toHaveKey($group->getKey())
                    ->not->toHaveKey($archivedGroup->getKey());

                return true;
            });
    });
});

describe('admin roles', function (): void {
    it('does not allow an admin role name on any access control', function (string $guardName): void {
        asSuperAdmin();

        $roleCount = Role::query()->count();

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => Authenticatable::PARTNER_ADMIN_ROLE,
                'guard_name' => $guardName,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'not_in']);

        expect(Role::query()->count())->toBe($roleCount);
    })->with(['web', 'api']);
});

describe('enterprise ai', function (): void {
    it('hides the Enterprise AI permission groups while Enterprise AI is disabled', function (): void {
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
