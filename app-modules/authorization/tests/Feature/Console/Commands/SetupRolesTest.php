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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use AdvisingApp\Authorization\Models\Permission;
use AdvisingApp\Authorization\Models\PermissionGroup;
use AdvisingApp\Authorization\AuthorizationRoleRegistry;

beforeEach(function () {
    DB::table('roles')->truncate();
    DB::table('model_has_roles')->truncate();
    DB::table('permissions')->truncate();
    DB::table('role_has_permissions')->truncate();
    DB::table('model_has_permissions')->truncate();
});

$createPermissions = function (array $permissions) {
    $permissionGroupId = PermissionGroup::create([
        'name' => 'Test',
    ])->id;

    $ids = [];

    Permission::insert(array_map(
        function (string $permissionName) use (&$ids, $permissionGroupId): array {
            return [
                'id' => $ids[] = (string) Str::orderedUuid(),
                'name' => $permissionName,
                'group_id' => $permissionGroupId,
                'guard_name' => 'web',
                'created_at' => now(),
            ];
        },
        $permissions,
    ));

    return [
        Permission::query()
            ->where('guard_name', 'web')
            ->pluck('id', 'name')
            ->all(),
        ...$ids,
    ];
};

it('can assign model permissions to roles', function () use ($createPermissions) {
    $registry = app(AuthorizationRoleRegistry::class);

    [$permissions, $testOnePermissionId, $testTwoPermissionId] = $createPermissions(['test.one', 'test.two', 'test.three', 'another.one']);

    $roles = [];

    $registry->registerRole('Test Role', [
        'model' => [
            'test' => [
                'one',
                'two',
            ],
        ],
    ], $roles, $permissions);

    expect($roles['Test Role'])
        ->toBe([$testOnePermissionId, $testTwoPermissionId]);
});

it('can assign all model permissions to roles', function () use ($createPermissions) {
    $registry = app(AuthorizationRoleRegistry::class);

    [$permissions, $testOnePermissionId, $testTwoPermissionId, $testThreePermissionId] = $createPermissions(['test.one', 'test.two', 'test.three', 'another.one']);

    $roles = [];

    $registry->registerRole('Test Role', [
        'model' => [
            'test' => [
                '*',
            ],
        ],
    ], $roles, $permissions);

    expect($roles['Test Role'])
        ->toBe([$testOnePermissionId, $testTwoPermissionId, $testThreePermissionId]);
});

it('can assign custom permissions to roles', function () use ($createPermissions) {
    $registry = app(AuthorizationRoleRegistry::class);

    [$permissions, $testOnePermissionId, $testTwoPermissionId] = $createPermissions(['testOne', 'testTwo', 'testThree']);

    $roles = [];

    $registry->registerRole('Test Role', [
        'custom' => [
            'testOne',
            'testTwo',
        ],
    ], $roles, $permissions);

    expect($roles['Test Role'])
        ->toBe([$testOnePermissionId, $testTwoPermissionId]);
});
