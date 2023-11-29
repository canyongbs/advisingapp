<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;

// TODO This should be queued
class RemoveRoleFromUsersInRoleGroup
{
    public function handle(RoleGroupRolePivot $pivot): void
    {
        $roleGroup = RoleGroup::findOrFail($pivot->role_group_id);
        $role = Role::findOrFail($pivot->role_id);

        $roleGroup->users()->each(function (User $user) use ($role, $roleGroup) {
            if ($this->roleShouldBeRemovedFromUser($user, $role, $roleGroup)) {
                $user->removeRole($role);
            }
        });
    }

    // TODO Extract this somewhere common as it's shared between this action and "RemoveRolesForRoleGroupFromUser"
    protected function roleShouldBeRemovedFromUser(User $user, Role $role, RoleGroup $roleGroup): bool
    {
        // If the user doesn't even have this role, no sense in removing
        if (! $user->hasRole($role)) {
            return false;
        }

        // If the user has this role directly, we should not remove it
        if ($user->hasBeenAssignedRoleDirectly($role)) {
            return false;
        }

        // If the role belongs to another role group that the user belongs to, we should not remove it
        if ($user->inheritsRoleFromAnotherRoleGroup($role, $roleGroup)) {
            return false;
        }

        return true;
    }
}
