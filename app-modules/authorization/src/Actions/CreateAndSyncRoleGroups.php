<?php

namespace Assist\Authorization\Actions;

use Illuminate\Support\Facades\File;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;

// TODO Refactor if we're going to use this moving forward
// Delete if we decide we just want to let orgs handle this manually through Filament
class CreateAndSyncRoleGroups
{
    public function handle(): void
    {
        collect(File::files(config_path('role_groups')))->each(function ($file) {
            $config = config("role_groups.{$file->getFilenameWithoutExtension()}");

            $roleGroup = RoleGroup::firstOrCreate([
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
