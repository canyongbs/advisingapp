<?php

namespace Tests;

use App\Models\User;
use App\Actions\Paths\ModulePath;
use Assist\Authorization\Models\Role;

function asSuperAdmin(?User $user = null): TestCase
{
    $superAdmin = $user ?? User::factory()->create();

    $superAdminRoles = Role::superAdmin()->get();

    $superAdmin->assignRole($superAdminRoles);

    return test()->actingAs($superAdmin);
}

function loadFixtureFromModule(string $module, string $file): mixed
{
    $modulePath = resolve(ModulePath::class);

    return json_decode(file_get_contents($modulePath($module, "tests/Fixtures/{$file}.json")), true);
}
