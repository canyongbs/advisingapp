<?php

namespace Assist\Authorization;

use App\Actions\Finders\ApplicationModules;

class AuthorizationRoleRegistry
{
    protected $moduleWebRoles = [];

    protected $moduleApiRoles = [];

    public function registerApiRoles(string $module, string $path)
    {
        $roles = resolve(ApplicationModules::class)
            ->moduleConfigDirectory(
                module: $module,
                path: $path
            );

        $this->moduleApiRoles[$module] = $roles;
    }

    public function registerWebRoles(string $module, string $path)
    {
        $roles = resolve(ApplicationModules::class)
            ->moduleConfigDirectory(
                module: $module,
                path: $path
            );

        $this->moduleWebRoles[$module] = $roles;
    }

    public function getModuleWebRoles(): array
    {
        return $this->moduleWebRoles;
    }

    public function getModuleApiRoles(): array
    {
        return $this->moduleApiRoles;
    }
}
