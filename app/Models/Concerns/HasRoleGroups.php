<?php

namespace App\Models\Concerns;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\Pivots\RoleGroupPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoleGroups
{
    public function roleGroups(): MorphToMany
    {
        return $this
            ->morphToMany(RoleGroup::class, 'role_groupable')
            ->using(RoleGroupPivot::class);
    }

    protected function inheritsRoleFromAnotherRoleGroup(Role $role, RoleGroup $roleGroup)
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
