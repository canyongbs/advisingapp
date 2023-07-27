<?php

namespace Assist\Authorization;

class AuthorizationRegistry
{
    protected $moduleWebPermissions = [];

    protected $moduleApiPermissions = [];

    public function registerApiPermissions(string $moduleName, array $permissions)
    {
        $this->moduleApiPermissions[$moduleName] = $permissions;
    }

    public function registerWebPermissions(string $moduleName, array $permissions)
    {
        $this->moduleWebPermissions[$moduleName] = $permissions;
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
