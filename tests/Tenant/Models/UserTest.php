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
use App\Features\UserGroupsFeature;
use App\Models\Authenticatable;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Spatie\Permission\PermissionRegistrar;

use function Tests\setEnterpriseAiEnabled;

describe('enterprise ai', function (): void {
    it('does not report a `ConversationalAi` license while Enterprise AI is disabled, and restores it once re-enabled', function (): void {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeFalse();

        assertDatabaseHas('licenses', [
            'user_id' => $user->getKey(),
            'type' => LicenseType::ConversationalAi,
            'deleted_at' => null,
        ]);

        setEnterpriseAiEnabled(true);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();
    });

    it('does not report a `ConversationalAi` license for a super admin while Enterprise AI is disabled', function (): void {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeTrue();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::ConversationalAi))->toBeFalse();
    });

    it('still reports other licenses while Enterprise AI is disabled', function (): void {
        $user = User::factory()->licensed([LicenseType::ConversationalAi, LicenseType::RetentionCrm])->create();

        setEnterpriseAiEnabled(false);

        expect($user->hasLicense(LicenseType::RetentionCrm))->toBeTrue()
            ->and($user->hasLicense([LicenseType::ConversationalAi, LicenseType::RetentionCrm]))->toBeFalse()
            ->and($user->hasAnyLicense([LicenseType::ConversationalAi, LicenseType::RetentionCrm]))->toBeTrue()
            ->and($user->hasAnyLicense(LicenseType::ConversationalAi))->toBeFalse();
    });

    it('does not grant a `ConversationalAi` license while Enterprise AI is disabled', function (): void {
        $user = User::factory()->create();

        setEnterpriseAiEnabled(false);

        expect($user->grantLicense(LicenseType::ConversationalAi))->toBeFalse();

        assertDatabaseMissing('licenses', [
            'user_id' => $user->getKey(),
            'type' => LicenseType::ConversationalAi,
        ]);
    });
});

describe('effective roles', function (): void {
    it('inherits roles from its groups', function (): void {
        $directRole = Role::factory()->create();
        $groupRole = Role::factory()->create();
        $otherRole = Role::factory()->create();

        $user = User::factory()->create();
        $user->assignRole($directRole);

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($groupRole);
        $userGroup->users()->attach($user);

        $user = $user->fresh();

        expect($user->getEffectiveRoles()->modelKeys())->toEqualCanonicalizing([$directRole->getKey(), $groupRole->getKey()])
            ->and($user->getRoleNames()->all())->toEqualCanonicalizing([$directRole->name, $groupRole->name])
            ->and($user->hasRole($groupRole))->toBeTrue()
            ->and($user->hasRole($groupRole->name))->toBeTrue()
            ->and($user->hasRole($groupRole->getKey()))->toBeTrue()
            ->and($user->hasRole($otherRole))->toBeFalse()
            ->and($user->hasAnyRole([$otherRole->name, $groupRole->name]))->toBeTrue()
            ->and($user->hasAllRoles([$directRole->name, $groupRole->name]))->toBeTrue()
            ->and($user->hasAllRoles([$groupRole->name, $otherRole->name]))->toBeFalse()
            ->and($user->hasExactRoles([$directRole->name, $groupRole->name]))->toBeTrue()
            ->and($user->roles()->pluck('roles.id')->all())->toBe([$directRole->getKey()]);
    });

    it('inherits permissions through the roles of its groups', function (): void {
        $role = Role::factory()->create();
        $role->givePermissionTo('user_group.view-any');

        $user = User::factory()->create();

        expect($user->can('user_group.view-any'))->toBeFalse();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        $user = $user->fresh();
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex($user);

        expect($user->can('user_group.view-any'))->toBeTrue()
            ->and($user->getPermissionsViaRoles()->pluck('name')->all())->toContain('user_group.view-any');
    });

    it('keeps granting roles through archived groups', function (): void {
        $role = Role::factory()->create();

        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->archived()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        expect($user->fresh()->hasRole($role))->toBeTrue();
    });

    it('stops granting roles through deleted groups', function (): void {
        $role = Role::factory()->create();

        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        expect($user->fresh()->hasRole($role))->toBeTrue();

        $userGroup->delete();

        expect($user->fresh()->hasRole($role))->toBeFalse()
            ->and(User::query()->role($role)->whereKey($user->getKey())->exists())->toBeFalse();
    });

    it('stops granting roles when the user is removed from the group', function (): void {
        $role = Role::factory()->create();
        $role->givePermissionTo('user_group.view-any');

        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        expect($user->fresh()->hasRole($role))->toBeTrue();

        $userGroup->users()->detach($user);

        $user = $user->fresh();
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex($user);

        expect($user->hasRole($role))->toBeFalse()
            ->and($user->can('user_group.view-any'))->toBeFalse();
    });

    it('stops granting roles when the role is detached from the group', function (): void {
        $role = Role::factory()->create();
        $role->givePermissionTo('user_group.view-any');

        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        expect($user->fresh()->hasRole($role))->toBeTrue();

        $userGroup->roles()->detach($role);

        $user = $user->fresh();
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex($user);

        expect($user->hasRole($role))->toBeFalse()
            ->and($user->can('user_group.view-any'))->toBeFalse();
    });

    it('never inherits an admin role attached to a group outside of the UI', function (string $adminRole): void {
        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->roles()->attach(Role::query()->where('name', $adminRole)->where('guard_name', 'web')->firstOrFail());
        $userGroup->users()->attach($user);

        $user = $user->fresh();
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex($user);

        expect($user->hasRole($adminRole))->toBeFalse()
            ->and($user->isAdmin())->toBeFalse()
            ->and($user->isSuperAdmin())->toBeFalse()
            ->and($user->isPartnerAdmin())->toBeFalse()
            ->and($user->isAiAdmin())->toBeFalse()
            ->and($user->getEffectiveRoles())->toBeEmpty()
            ->and($user->can('user_group.view-any'))->toBeFalse()
            ->and(User::query()->role($adminRole)->whereKey($user->getKey())->exists())->toBeFalse()
            ->and(User::query()->withoutRole($adminRole)->whereKey($user->getKey())->exists())->toBeTrue();
    })->with(Authenticatable::ADMIN_ROLES);

    it('still recognises an admin role assigned directly', function (): void {
        $user = User::factory()->create();
        $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

        expect($user->fresh()->isSuperAdmin())->toBeTrue()
            ->and(User::query()->role(Authenticatable::SUPER_ADMIN_ROLE)->whereKey($user->getKey())->exists())->toBeTrue();
    });

    it('does not exclude users from `WithoutAnyAdmin` when admin role is attached only through a group', function (): void {
        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->roles()->attach(Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->where('guard_name', 'web')->firstOrFail());
        $userGroup->users()->attach($user);

        expect(User::query()->tap(new WithoutAnyAdmin())->whereKey($user->getKey())->exists())->toBeTrue();
    });

    it('keeps counting license seats when admin role is attached only through a group', function (): void {
        $user = User::factory()->licensed(LicenseType::RetentionCrm)->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->roles()->attach(Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->where('guard_name', 'web')->firstOrFail());
        $userGroup->users()->attach($user);

        expect(LicenseType::RetentionCrm->getSeatsInUse())->toBe(1);
    });

    it('matches users with a role directly or through their groups in the `role` and `withoutRole` scopes', function (): void {
        $role = Role::factory()->create();

        $directUser = User::factory()->create();
        $directUser->assignRole($role);

        $groupUser = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($groupUser);

        $otherUser = User::factory()->create();

        expect(User::query()->role($role)->pluck('id')->all())
            ->toEqualCanonicalizing([$directUser->getKey(), $groupUser->getKey()])
            ->and(User::query()->role($role->name)->pluck('id')->all())
            ->toEqualCanonicalizing([$directUser->getKey(), $groupUser->getKey()])
            ->and(User::query()->withoutRole($role)->pluck('id')->all())
            ->toContain($otherUser->getKey())
            ->not->toContain($directUser->getKey())
            ->not->toContain($groupUser->getKey());
    });

    it('ignores group roles while the `UserGroupsFeature` is inactive', function (): void {
        $role = Role::factory()->create();
        $role->givePermissionTo('user_group.view-any');

        $user = User::factory()->create();

        $userGroup = UserGroup::factory()->create();
        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        expect($user->fresh()->hasRole($role))->toBeTrue();

        UserGroupsFeature::deactivate();

        $user = $user->fresh();
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex($user);

        expect($user->hasRole($role))->toBeFalse()
            ->and($user->can('user_group.view-any'))->toBeFalse()
            ->and(User::query()->role($role)->whereKey($user->getKey())->exists())->toBeFalse();
    });
});
