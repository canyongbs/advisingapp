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
use App\Actions\Finders\ApplicationModules;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\Actions\CreatePermissionsForModel;

beforeEach(function () {
    Relation::morphMap([
        'user' => Mockery_3_App_Models_User::class,
    ]);

    (new Helpers())->truncateTables();
});

it('will respect model permission overrides', function () {
    // When a model overrides the default configuration for permissions
    partialMock(User::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('getWebPermissions')
            ->once()
            ->andReturn(collect(['*.test']));
    });

    // And the CreatePermissionsForModel action is run
    $action = resolve(CreatePermissionsForModel::class);
    $action->handle(User::class);

    // Our database should reflect the appropriate permissions
    assertDatabaseHas('permissions', [
        'name' => 'user.*.test',
        'guard_name' => 'web',
    ]);

    // Respecting the override and ignoring the defaults
    assertDatabaseMissing('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});

it('will respect model permission extensions', function () {
    // When a model extends the default configuration for permissions
    partialMock(User::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('getWebPermissions')
            ->once()
            ->andReturn(collect([
                '*.test',
                ...resolve(ApplicationModules::class)->moduleConfig('authorization', 'permissions/web/model'),
            ]));
    });

    // And the CreatePermissionsForModel action is run
    $action = resolve(CreatePermissionsForModel::class);
    $action->handle(User::class);

    // Our database should reflect the appropriate permissions the model has extended
    assertDatabaseHas('permissions', [
        'name' => 'user.*.test',
        'guard_name' => 'web',
    ]);

    // While also respecting the defaults that the application provides
    assertDatabaseHas('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});
