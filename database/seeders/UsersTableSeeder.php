<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Assist\Authorization\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@assist.com',
            'password' => Hash::make('password'),
        ]);

        $superAdminRoles = Role::superAdmin()->get();

        $superAdmin->assignRole($superAdminRoles);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@assist.com',
            'password' => Hash::make('password'),
        ]);
    }
}
