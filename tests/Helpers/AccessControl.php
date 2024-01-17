<?php

namespace Tests\Helpers;

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Authorization\Enums\LicenseType;

function testResourceRequiresPermissionForAccess(string $resource, string $permission, string $method)
{
    test("{$resource} {$method} is gated with proper access control", function () use ($permission, $resource, $method) {
        $user = User::factory()->licensed(LicenseType::cases())->create();

        actingAs($user)
            ->get(
                $resource::getUrl($method)
            )->assertForbidden();

        $user->givePermissionTo($permission);

        actingAs($user)
            ->get(
                $resource::getUrl($method)
            )->assertSuccessful();
    });
}
