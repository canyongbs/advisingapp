<?php

namespace Assist\Authorization\Policies;

use App\Models\User;
use Assist\Authorization\Models\RoleGroup;

class RoleGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RoleGroup $roleGroup): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RoleGroup $roleGroup): bool
    {
        return true;
    }

    public function delete(User $user, RoleGroup $roleGroup): bool
    {
        return true;
    }

    public function restore(User $user, RoleGroup $roleGroup): bool
    {
        return true;
    }

    public function forceDelete(User $user, RoleGroup $roleGroup): bool
    {
        return true;
    }
}
