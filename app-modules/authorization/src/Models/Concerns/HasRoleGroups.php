<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Authorization\Models\Concerns;

use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoleGroups
{
    public function roleGroups(): BelongsToMany
    {
        return $this
            ->belongsToMany(RoleGroup::class)
            ->withTimestamps();
    }

    public function inheritsRoleFromAnotherRoleGroup(Role $role, RoleGroup $roleGroup)
    {
        // If the user belongs to another RoleGroup that implements this Role
        // We want to leave this role in place
        $inherits = false;

        $this->roleGroups->each(function (RoleGroup $belongedToRoleGroup) use (&$inherits, $role, $roleGroup) {
            if ($belongedToRoleGroup->id === $roleGroup->id) {
                return;
            }

            if ($belongedToRoleGroup->roles->contains($role)) {
                $inherits = true;
            }
        });

        return $inherits;
    }
}
