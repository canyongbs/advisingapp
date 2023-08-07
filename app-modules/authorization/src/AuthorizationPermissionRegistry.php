<?php

namespace Assist\Authorization;

use App\Actions\Finders\ApplicationModules;

class AuthorizationPermissionRegistry
{
    protected $moduleWebPermissions = [];

    protected $moduleApiPermissions = [];

    public function registerApiPermissions(string $module, string $path)
    {
        $permissions = resolve(ApplicationModules::class)
            ->moduleConfig(
                module: $module,
                path: $path
            );

        if (! blank($permissions)) {
            $this->moduleApiPermissions[$module] = $permissions;
        }
    }

    public function registerWebPermissions(string $module, string $path)
    {
        $permissions = resolve(ApplicationModules::class)
            ->moduleConfig(
                module: $module,
                path: $path
            );

        if (! blank($permissions)) {
            $this->moduleWebPermissions[$module] = $permissions;
        }
    }

    public function getModuleWebPermissions(): array
    {
        return $this->moduleWebPermissions;
    }

    public function getModuleApiPermissions(): array
    {
        return $this->moduleApiPermissions;
    }
}
