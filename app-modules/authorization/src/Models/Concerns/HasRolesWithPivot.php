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
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\PermissionRegistrar;
use App\Actions\Finders\ApplicationModules;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesWithPivot
{
    use HasRoles {
        HasRoles::assignRole as protected originalAssignRole;
    }

    public function roles(): BelongsToMany
    {
        $relation = $this->morphToMany(
            // TODO make a slightly better helper similar to the config helper that still allows
            // Us to pass an exact path to the key within the config, not just the path of the file
            resolve(ApplicationModules::class)->moduleConfig('authorization', 'permission')['models']['role'],
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            PermissionRegistrar::$pivotRole
        )->withPivot('via');

        if (! PermissionRegistrar::$teams) {
            return $relation;
        }

        return $relation->wherePivot(PermissionRegistrar::$teamsKey, getPermissionsTeamId())
            ->where(function ($q) {
                $teamField = config('permission.table_names.roles') . '.' . PermissionRegistrar::$teamsKey;
                $q->whereNull($teamField)->orWhere($teamField, getPermissionsTeamId());
            });
    }

    public function hasBeenAssignedRoleDirectly(Role $role)
    {
        return $this->roles()
            ->where('id', $role->id)
            ->where('via', ModelHasRolesViaEnum::Direct)
            ->exists();
    }

    public function assignRoleViaRoleGroup(Role $role): void
    {
        $this->assignRole([$this->roles, $role]);

        $this->roles()->where('id', $role->id)->first()->pivot->update([
            'via' => ModelHasRolesViaEnum::RoleGroup,
        ]);
    }
}
