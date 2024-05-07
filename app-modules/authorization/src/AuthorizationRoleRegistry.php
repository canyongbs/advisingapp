<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization;

use Exception;
use App\Models\Tenant;
use Illuminate\Support\Facades\File;
use AdvisingApp\Authorization\Models\Permission;

class AuthorizationRoleRegistry
{
    protected array $webPermissions = [];

    protected array $apiPermissions = [];

    protected array $webRoles = [];

    protected array $apiRoles = [];

    public function __construct()
    {
        if (! Tenant::current()) {
            throw new Exception('No current tenant set');
        }

        $this->webPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->pluck('id', 'name')
            ->all();

        $this->apiPermissions = Permission::query()
            ->where('guard_name', 'api')
            ->pluck('id', 'name')
            ->all();
    }

    public static function register(string $class): void
    {
        app()->resolving(AuthorizationRoleRegistry::class, function () use ($class) {
            app()->call($class);
        });
    }

    public function registerApiRoles(string $module, string $path)
    {
        $this->registerRoles($module, $path, $this->apiRoles, $this->apiPermissions);
    }

    public function registerWebRoles(string $module, string $path)
    {
        $this->registerRoles($module, $path, $this->webRoles, $this->webPermissions);
    }

    public function getWebRoles(): array
    {
        return $this->webRoles;
    }

    public function getApiRoles(): array
    {
        return $this->apiRoles;
    }

    protected function registerRoles(string $module, string $path, &$roleRegistry, &$permissionRegistry): void
    {
        foreach (File::files(base_path("app-modules/{$module}/config/{$path}")) as $file) {
            $roleName = "{$module}.{$file->getFilenameWithoutExtension()}";

            $roleRegistry[$roleName] ??= [];

            $permissions = require $file->getPathname();

            if ($permissions === ['*']) {
                $roleRegistry[$roleName] = array_values($permissionRegistry);

                continue;
            }

            foreach ($permissions['model'] ?? [] as $model => $operations) {
                if (is_string($operations)) {
                    dd($operations, $module, $path);
                }

                foreach ($operations as $operation) {
                    if ($operation === '*') {
                        foreach ($permissionRegistry as $permissionName => $permissionId) {
                            if (! str($permissionName)->startsWith("{$model}.")) {
                                continue;
                            }

                            $roleRegistry[$roleName][] = $permissionId;
                        }

                        continue;
                    }

                    $permissionName = "{$model}.{$operation}";

                    if (! array_key_exists($permissionName, $permissionRegistry)) {
                        continue;
                    }

                    $roleRegistry[$roleName][] = $permissionRegistry[$permissionName];
                }
            }

            foreach ($permissions['custom'] ?? [] as $permissionName) {
                if (! array_key_exists($permissionName, $permissionRegistry)) {
                    continue;
                }

                $roleRegistry[$roleName][] = $permissionRegistry[$permissionName];
            }

            $roleRegistry[$roleName] = array_unique($roleRegistry[$roleName]);
        }
    }
}
