<?php

namespace App\Actions\RolesAndPermissions;

use App\Models\Role;
use App\Models\RoleGroup;
use Illuminate\Support\Facades\File;

class SyncRoleGroups
{
    public function handle(): void
    {
        collect(File::files(config_path('role_groups')))->each(function ($file) {
            $config = config("role_groups.{$file->getFilenameWithoutExtension()}");

            $roleGroup = RoleGroup::create([
                'name' => $config['name'],
            ]);

            collect($config['roles']['api'])->each(function ($apiRole) use ($roleGroup) {
                $role = Role::web()->where('name', $apiRole)->first();

                $roleGroup->roles()->syncWithoutDetaching($role->id);
            });

            collect($config['roles']['web'])->each(function ($webRole) use ($roleGroup) {
                $role = Role::web()->where('name', $webRole)->first();

                $roleGroup->roles()->syncWithoutDetaching($role->id);
            });
        });
    }
}
