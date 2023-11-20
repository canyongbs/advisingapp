<?php

namespace Assist\Authorization\Actions;

use Assist\Authorization\Models\Role;
use Assist\Authorization\AuthorizationRoleRegistry;

class CreateRoles
{
    public function handle(): void
    {
        $roleRegistry = resolve(AuthorizationRoleRegistry::class);

        foreach ($roleRegistry->getModuleWebRoles() as $module => $roles) {
            foreach ($roles as $role) {
                Role::firstOrCreate([
                    'name' => "{$module}.{$role}",
                    'guard_name' => 'web',
                ]);
            }
        }

        foreach ($roleRegistry->getModuleApiRoles() as $module => $roles) {
            foreach ($roles as $role) {
                Role::firstOrCreate([
                    'name' => "{$module}.{$role}",
                    'guard_name' => 'api',
                ]);
            }
        }
    }
}
