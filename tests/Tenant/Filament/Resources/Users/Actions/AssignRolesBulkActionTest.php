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

use AdvisingApp\Authorization\Models\Role;
use App\Filament\Resources\Users\Actions\AssignRolesBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('assigns roles to the selected users without removing their existing roles', function (): void {
    asSuperAdmin();

    $existingRole = Role::factory()->create(['guard_name' => 'web']);
    $newRole = Role::factory()->create(['guard_name' => 'web']);

    $users = User::factory()->count(2)->create();
    $users->each(fn (User $user) => $user->assignRole($existingRole));

    Livewire::test(ListUsers::class)
        ->selectTableRecords($users)
        ->callAction(TestAction::make(AssignRolesBulkAction::class)->table()->bulk(), data: [
            'replace' => false,
            'roles' => [$newRole->name],
        ])
        ->assertHasNoActionErrors()
        ->assertNotified('Assigned Roles');

    $users->each(function (User $user) use ($existingRole, $newRole): void {
        expect($user->roles()->pluck('name')->all())
            ->toEqualCanonicalizing([$existingRole->name, $newRole->name]);
    });
});

it('lets a super admin assign admin roles', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->selectTableRecords([$user])
        ->callAction(TestAction::make(AssignRolesBulkAction::class)->table()->bulk(), data: [
            'replace' => false,
            'roles' => [Authenticatable::PARTNER_ADMIN_ROLE],
        ])
        ->assertHasNoActionErrors();

    expect($user->refresh()->isPartnerAdmin())->toBeTrue();
});

it('does not let a non-super-admin assign admin roles', function (): void {
    $manager = user(permissions: ['user.view-any', 'user.*.view', 'user.*.update']);

    actingAs($manager);

    Livewire::test(ListUsers::class)
        ->selectTableRecords([$manager])
        ->mountAction(TestAction::make(AssignRolesBulkAction::class)->table()->bulk())
        ->assertFormFieldExists('roles', function (Select $field): bool {
            expect($field->getOptions())->not->toHaveKeys(Authenticatable::ADMIN_ROLES);

            return true;
        })
        ->callMountedAction([
            'replace' => false,
            'roles' => [Authenticatable::SUPER_ADMIN_ROLE],
        ]);

    expect($manager->refresh()->roles)->toBeEmpty();
});

it('keeps the roles a non-super-admin can not assign when replacing roles', function (): void {
    $partnerAdmin = User::factory()->create();
    $partnerAdmin->assignRole(Authenticatable::PARTNER_ADMIN_ROLE);

    $oldRole = Role::factory()->create(['guard_name' => 'web']);
    $newRole = Role::factory()->create(['guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole([Authenticatable::PARTNER_ADMIN_ROLE, $oldRole]);

    actingAs($partnerAdmin);

    Livewire::test(ListUsers::class)
        ->selectTableRecords([$user])
        ->callAction(TestAction::make(AssignRolesBulkAction::class)->table()->bulk(), data: [
            'replace' => true,
            'roles' => [$newRole->name],
        ])
        ->assertHasNoActionErrors();

    expect($user->roles()->pluck('name')->all())
        ->toEqualCanonicalizing([Authenticatable::PARTNER_ADMIN_ROLE, $newRole->name]);
});
