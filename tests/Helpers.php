<?php

namespace Tests;

use App\Models\User;
use Assist\Authorization\Models\Role;

function asSuperAdmin(): TestCase
{
    $superAdmin = User::factory()->create();

    $superAdminRoles = Role::superAdmin()->get();

    $superAdmin->assignRole($superAdminRoles);

    return test()->actingAs($superAdmin);
}
