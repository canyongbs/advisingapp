<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Mockery\MockInterface;

use function Pest\Laravel\partialMock;

use App\Actions\Finders\ApplicationModules;
use AdvisingApp\Authorization\Tests\Helpers;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Authorization\Actions\CreatePermissionsForModel;

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
