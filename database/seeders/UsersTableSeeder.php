<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\RoleGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@assist.com',
            'password' => bcrypt('password'),
        ]);

        $superAdminRoles = Role::superAdmin()->get();

        $superAdmin->assignRole($superAdminRoles);

        /** Admin Role Group */
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@assist.com',
            'password' => Hash::make('password'),
        ]);

        $adminRoleGroup = RoleGroup::admin()->first();

        $admin->roleGroups()->syncWithoutDetaching($adminRoleGroup);
    }
}
