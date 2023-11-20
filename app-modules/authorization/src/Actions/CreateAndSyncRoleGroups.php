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
