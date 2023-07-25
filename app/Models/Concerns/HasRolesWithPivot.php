<?php

namespace App\Models\Concerns;

use App\Models\Role;
use App\Enums\ModelHasRolesViaEnum;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesWithPivot
{
    use HasRoles {
        assignRole as protected originalAssignRole;
    }

    public function roles(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.role'),
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
        return $this->roles
            ->contains($role)
            ->where('via', ModelHasRolesViaEnum::Direct);
    }
}
