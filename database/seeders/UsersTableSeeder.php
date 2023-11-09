<?php

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
