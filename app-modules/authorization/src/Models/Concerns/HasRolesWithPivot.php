<?php

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
