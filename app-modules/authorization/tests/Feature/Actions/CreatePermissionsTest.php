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

use App\Models\User;
use Mockery\MockInterface;

use function Pest\Laravel\partialMock;

use Assist\Authorization\Tests\Helpers;
use App\Actions\Finders\ApplicationModels;
use Assist\Authorization\Models\Permission;

use function Pest\Laravel\assertDatabaseHas;

use Spatie\Permission\Commands\CreatePermission;
use Assist\Authorization\Actions\CreatePermissions;
use Assist\Authorization\AuthorizationPermissionRegistry;

beforeEach(function () {
    (new Helpers())->truncateTables();
});

it('will create appropriate permissions for all models', function () {
    partialMock(ApplicationModels::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('implementingPermissions')
            ->andReturn(collect([
                User::class,
            ]));
    });

    /** @var CreatePermission $createPermissionsAction */
    $createPermissionsAction = partialMock(CreatePermissions::class, function (MockInterface $mock) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCustomPermissions')
            ->andReturn();
    });

    $createPermissionsAction->handle();

    assertDatabaseHas('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});

it('will create appropriate custom permissions', function () {
    /** @var CreatePermission $createPermissionsAction */
    $createPermissionsAction = partialMock(CreatePermissions::class, function (MockInterface $mock) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createModelPermissions')
            ->andReturn();
    });

    partialMock(AuthorizationPermissionRegistry::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('getModuleWebPermissions')
            ->andReturn(['new-module' => ['dashboard.access']]);

        $mock
            ->shouldReceive('getModuleApiPermissions')
            ->andReturn(['new-module' => ['dashboard.queries']]);
    });

    // When we run the CreatePermissions action
    $createPermissionsAction->handle();

    // We should have created the records that were added to the registry from the module
    assertDatabaseHas('permissions', [
        'name' => 'new-module.dashboard.access',
        'guard_name' => 'web',
    ]);

    assertDatabaseHas('permissions', [
        'name' => 'new-module.dashboard.queries',
        'guard_name' => 'api',
    ]);

    expect(Permission::get())->toHaveCount(2);
});
