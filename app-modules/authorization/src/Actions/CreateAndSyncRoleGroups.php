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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Actions;

use Illuminate\Support\Facades\File;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\RoleGroup;

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
