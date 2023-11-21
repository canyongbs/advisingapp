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
