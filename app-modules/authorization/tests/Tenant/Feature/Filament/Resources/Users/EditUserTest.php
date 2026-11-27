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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\Team\Models\Department;
use App\Enums\RetentionCrmRestriction;
use App\Features\UserGroupsFeature;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Authenticatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

use STS\FilamentImpersonate\Actions\Impersonate;
use STS\FilamentImpersonate\Facades\Impersonation;

use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

it('renders impersonate button for non super admin users when user is super admin', function (): void {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
});

it('does not render super admin profile for regular user', function (): void {
    // Create a super admin user
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    // Verify super admin user exists
    assertDatabaseHas('users', ['id' => $superAdmin->id]);

    // Create another user
    $user = User::factory()->create();
    actingAs($user);

    // Attempt to load the EditUser component with the super admin's route key
    Livewire::test(EditUser::class, ['record' => $superAdmin->getRouteKey()])
        ->assertNotFound();
});

it('does not render impersonate button for super admin users at all', function (): void {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = Livewire::test(EditUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('allows super admin user to impersonate', function (): void {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    $component = Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect(Impersonation::isImpersonating())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

it('allows user with permission to impersonate', function (): void {
    $first = User::factory()->create();
    $first->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update');
    asSuperAdmin($first);

    $second = User::factory()->create();

    $component = Livewire::test(EditUser::class, [
        'record' => $second->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect(Impersonation::isImpersonating())->toBeTrue();
    expect(Auth::id())->toBe($second->id);
});

describe('enterprise ai', function (): void {
    it('hides the `ConversationalAi` license toggle while Enterprise AI is disabled', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldExists('conversational_ai_enabled')
            ->assertFormFieldExists('retention_crm_enabled');

        setEnterpriseAiEnabled(false);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldDoesNotExist('conversational_ai_enabled')
            ->assertFormFieldExists('retention_crm_enabled');
    });
});

describe('affiliation', function (): void {
    it('saves the department and groups', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $department = Department::factory()->create();
        $userGroup = UserGroup::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'team_id' => $department->getKey(),
                'userGroups' => [$userGroup->getKey()],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        expect($user->team_id)->toBe($department->getKey())
            ->and($user->userGroups->modelKeys())->toBe([$userGroup->getKey()]);
    });

    it('does not offer archived groups', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $userGroup = UserGroup::factory()->create();
        $archivedUserGroup = UserGroup::factory()->archived()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldExists('userGroups', fn (Select $field): bool => array_key_exists($userGroup->getKey(), $field->getOptions())
                && ! array_key_exists($archivedUserGroup->getKey(), $field->getOptions()));
    });

    it('hides the groups without the `user_group.view-any` permission', function (): void {
        $user = User::factory()->create();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldHidden('userGroups');

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update', 'user_group.view-any'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldVisible('userGroups');
    });

    it('hides the groups while the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldHidden('userGroups');
    });
});

describe('access control', function (): void {
    it('saves the roles', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $existingRole = Role::factory()->create(['guard_name' => 'web']);
        $user->assignRole($existingRole);
        $role = Role::factory()->create(['guard_name' => 'web']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertSchemaStateSet(['roles' => [$existingRole->getKey()]])
            ->fillForm(['roles' => [$role->getKey()]])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->roles()->pluck('roles.id')->all())->toBe([$role->getKey()]);
    });

    it('only offers `web` roles', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $webRole = Role::factory()->create(['guard_name' => 'web']);
        $apiRole = Role::factory()->create(['guard_name' => 'api']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldExists('roles', fn (Select $field): bool => array_key_exists($webRole->getKey(), $field->getOptions())
                && ! array_key_exists($apiRole->getKey(), $field->getOptions()))
            ->fillForm(['roles' => [$apiRole->getKey()]])
            ->call('save')
            ->assertHasFormErrors(['roles.0']);

        expect($user->roles()->count())->toBe(0);
    });

    it('does not let a user who is not a super admin assign admin roles', function (string $adminRoleName): void {
        $user = User::factory()->create();
        $adminRole = Role::query()->where('name', $adminRoleName)->where('guard_name', 'web')->firstOrFail();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update', 'role.view-any'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldExists('roles', fn (Select $field): bool => ! array_key_exists($adminRole->getKey(), $field->getOptions()))
            ->fillForm(['roles' => [$adminRole->getKey()]])
            ->call('save')
            ->assertHasFormErrors(['roles.0']);

        expect($user->roles()->count())->toBe(0);
    })->with(Authenticatable::ADMIN_ROLES);

    it('lets a super admin assign admin roles', function (): void {
        asSuperAdmin();

        $user = User::factory()->create();
        $adminRole = Role::query()->superAdmin()->firstOrFail();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm(['roles' => [$adminRole->getKey()]])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->roles()->pluck('roles.id')->all())->toBe([$adminRole->getKey()]);
    });

    it('keeps the admin roles that a user who is not a super admin cannot see', function (): void {
        $user = User::factory()->create();
        $adminRole = Role::query()->where('name', Authenticatable::PARTNER_ADMIN_ROLE)->where('guard_name', 'web')->firstOrFail();
        $role = Role::factory()->create(['guard_name' => 'web']);
        $user->assignRole($adminRole);

        // Partner admins can edit each other, but cannot see admin roles.
        actingAs(User::factory()->create()->assignRole(Authenticatable::PARTNER_ADMIN_ROLE));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertSchemaStateSet(['roles' => []])
            ->fillForm(['roles' => [$role->getKey()]])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->roles()->pluck('roles.id')->all())->toEqualCanonicalizing([$adminRole->getKey(), $role->getKey()]);
    });

    it('hides the roles without the `role.view-any` permission', function (): void {
        $user = User::factory()->create();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldHidden('roles');

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update', 'role.view-any'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldVisible('roles');
    });
});

describe('licenses', function (): void {
    it('changes licenses and the Retention CRM restriction only when the form is saved', function (): void {
        asSuperAdmin();

        $user = User::factory()->licensed([LicenseType::RetentionCrm])->create();

        $component = Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'retention_crm_enabled' => false,
                'conversational_ai_enabled' => true,
                'retention_crm_restriction' => RetentionCrmRestriction::FacultyMember->value,
            ]);

        expect($user->refresh()->hasLicense(LicenseType::RetentionCrm))->toBeTrue()
            ->and($user->hasLicense(LicenseType::ConversationalAi))->toBeFalse();

        $component
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        expect($user->hasLicense(LicenseType::RetentionCrm))->toBeFalse()
            ->and($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue()
            ->and($user->retention_crm_restriction)->toBe(RetentionCrmRestriction::FacultyMember);
    });

    it('shows a validation error when assigning a license with no seats available', function (): void {
        asSuperAdmin();

        $licenseSettings = app(LicenseSettings::class);
        $licenseSettings->data->limits->retentionCrmSeats = 1;
        $licenseSettings->save();

        User::factory()->licensed([LicenseType::RetentionCrm])->create();
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'retention_crm_enabled' => true,
            ])
            ->call('save')
            ->assertHasFormErrors(['retention_crm_enabled']);

        expect($user->refresh()->hasLicense(LicenseType::RetentionCrm))->toBeFalse();
    });

    it('does not change licenses without the `license.create` permission', function (): void {
        $user = User::factory()->licensed([LicenseType::RetentionCrm])->create();

        actingAs(User::factory()->create()->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update'));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertFormFieldDisabled('retention_crm_enabled')
            ->fillForm([
                'retention_crm_enabled' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->refresh()->hasLicense(LicenseType::RetentionCrm))->toBeTrue();
    });
});
