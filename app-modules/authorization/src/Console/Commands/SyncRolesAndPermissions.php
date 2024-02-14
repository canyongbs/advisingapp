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

namespace AdvisingApp\Authorization\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use AdvisingApp\Authorization\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Actions\Finders\ApplicationModules;
use AdvisingApp\Authorization\Models\Permission;
use App\Registries\RoleBasedAccessControlRegistry;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class SyncRolesAndPermissions extends Command
{
    use TenantAware;

    protected $signature = 'roles-and-permissions:sync {--tenant=*}';

    protected $description = 'This command will sync all roles and permissions defined in the roles and permissions config files.';

    public function handle(): int
    {
        // TODO Put handling in place to prevent this from being run in production IF it has already been run once
        // We are going to introduce a convention for "one-time" operations similar to Laravel migrations in order to handle this

        $this->populateRegistries();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $currentTenant = Tenant::current();

        Artisan::call(
            command: SetupRoles::class,
            parameters: [
                '--tenant' => $currentTenant->id,
            ],
            outputBuffer: $this->output,
        );

        Artisan::call(
            command: SetupPermissions::class,
            parameters: [
                '--tenant' => $currentTenant->id,
            ],
            outputBuffer: $this->output,
        );

        $this->line('Syncing Web permissions...');
        $this->syncWebPermissions();
        $this->info('Web permissions synced successfully!');

        $this->line('Syncing API permissions...');
        $this->syncApiPermissions();
        $this->info('API permissions synced successfully!');

        return self::SUCCESS;
    }

    protected function populateRegistries(): void
    {
        RoleBasedAccessControlRegistry::getRegistries()
            ->each(fn ($registry) => resolve($registry)->registerRolesAndPermissions());
    }

    protected function syncWebPermissions(): void
    {
        $this->withProgressBar(
            Role::where('guard_name', 'web')
                ->where('name', '!=', 'authorization.super_admin')
                ->cursor(),
            function (Role $role) {
                $this->syncPermissionFor('web', $role);
            }
        );

        $this->newLine();
    }

    protected function syncApiPermissions(): void
    {
        $this->withProgressBar(
            Role::where('guard_name', 'api')
                ->where('name', '!=', 'authorization.super_admin')
                ->cursor(),
            function (Role $role) {
                $this->syncPermissionFor('api', $role);
            }
        );

        $this->newLine();
    }

    protected function syncPermissionFor(string $guard, Role $role): void
    {
        // This is assuming that our roles are named in the following convention: {module}.{role}
        [$module, $roleFileName] = explode('.', $role->name);

        $permissions = resolve(ApplicationModules::class)
            ->moduleConfig(
                module: $module,
                path: "roles/{$guard}/{$roleFileName}"
            );

        collect($permissions)->map(function ($specificPermissions, $permissionConvention) use ($role, $guard) {
            if (blank($specificPermissions)) {
                return;
            }

            collect($specificPermissions)
                ->map(function ($specificPermission, $resource) use ($role, $guard) {
                    if (! is_array($specificPermission)) {
                        $this->syncCustomPermissions($role, $specificPermission, $guard);
                    } else {
                        $this->syncModelPermissions($role, $resource, $specificPermission, $guard);
                    }
                });
        });
    }

    protected function syncCustomPermissions(Role $role, string $specificPermission, string $guard): void
    {
        $foundPermissions = Permission::firstWhere([
            'name' => $specificPermission,
            'guard_name' => $guard,
        ])->name;

        $role->syncPermissions([$role->permissions, $foundPermissions]);
    }

    protected function syncModelPermissions(Role $role, string $resource, array $specificPermission, string $guard): void
    {
        if (count($specificPermission) === 1 && $specificPermission[0] === '*') {
            $foundPermissions = Permission::where('name', 'like', "{$resource}.%")
                ->where('guard_name', $guard)
                ->pluck('name');
        } else {
            $permissionNames = collect($specificPermission)->map(function ($permission) use ($resource) {
                return "{$resource}.{$permission}";
            });

            $foundPermissions = collect($specificPermission)->map(function () use ($permissionNames, $guard) {
                return Permission::where('guard_name', $guard)
                    ->whereIn('name', $permissionNames)
                    ->pluck('name');
            });
        }

        $role->givePermissionTo($foundPermissions);
    }
}
