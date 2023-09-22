<?php

use App\Models\User;
use Mockery\MockInterface;
use Assist\Authorization\Tests\Helpers;
use App\Actions\Finders\ApplicationModules;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\Actions\CreatePermissionsForModel;

beforeEach(function () {
    Relation::morphMap([
        'user' => \Mockery_3_App_Models_User::class,
    ]);

    (new Helpers())->truncateTables();
});

it('will respect model permission overrides', function () {
    // When a model overrides the default configuration for permissions
    $this->partialMock(User::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('getWebPermissions')
            ->once()
            ->andReturn(collect(['*.test']));
    });

    // And the CreatePermissionsForModel action is run
    $action = resolve(CreatePermissionsForModel::class);
    $action->handle(User::class);

    // Our database should reflect the appropriate permissions
    $this->assertDatabaseHas('permissions', [
        'name' => 'user.*.test',
        'guard_name' => 'web',
    ]);

    // Respecting the override and ignoring the defaults
    $this->assertDatabaseMissing('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});

it('will respect model permission extensions', function () {
    // When a model extends the default configuration for permissions
    $this->partialMock(User::class, function (MockInterface $mock) {
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
    $this->assertDatabaseHas('permissions', [
        'name' => 'user.*.test',
        'guard_name' => 'web',
    ]);

    // While also respecting the defaults that the application provides
    $this->assertDatabaseHas('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});
