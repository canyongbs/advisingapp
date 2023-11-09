<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;

class SuperAdminProfileSeeder extends Seeder
{
    public function run(): void
    {
        /** @var RoleGroup $roleGroup */
        $roleGroup = RoleGroup::create([
            'name' => 'Super Administrator',
        ]);

        $roleGroup->roles()->sync(Role::where('name', 'authorization.super_admin')->get());
    }
}
