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

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Assist\Authorization\Models\RoleGroup;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRoleGroup = RoleGroup::where('name', 'Super Administrator')->firstOrFail();

        if (app()->environment('local')) {
            $superAdmin = User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'sampleadmin@advising.app',
                'password' => Hash::make('password'),
            ]);

            $superAdmin->roleGroups()->sync($superAdminRoleGroup);
        }

        collect(config('internal-users.emails'))->each(function ($email) use ($superAdminRoleGroup) {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                $user = User::factory()->create([
                    'name' => Str::title(Str::replace('.', ' ', Str::before($email, '@'))),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_external' => true,
                ]);
            }

            $user->roleGroups()->sync($superAdminRoleGroup);
        });
    }
}
