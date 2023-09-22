<?php

use App\Models\User;
use Mockery\MockInterface;
use Assist\Authorization\Tests\Helpers;
use App\Actions\Finders\ApplicationModels;
use Assist\Authorization\Models\Permission;
use Spatie\Permission\Commands\CreatePermission;
use Assist\Authorization\Actions\CreatePermissions;
use Assist\Authorization\AuthorizationPermissionRegistry;

beforeEach(function () {
    (new Helpers())->truncateTables();
});

it('will create appropriate permissions for all models', function () {
    $this->partialMock(ApplicationModels::class, function (MockInterface $mock) {
        $mock
            ->shouldReceive('implementingPermissions')
            ->andReturn(collect([
                User::class,
            ]));
    });

    /** @var CreatePermission $createPermissionsAction */
    $createPermissionsAction = $this->partialMock(CreatePermissions::class, function (MockInterface $mock) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCustomPermissions')
            ->andReturn();
    });

    $createPermissionsAction->handle();

    $this->assertDatabaseHas('permissions', [
        'name' => 'user.*.view',
        'guard_name' => 'web',
    ]);
});

it('will create appropriate custom permissions', function () {
    /** @var CreatePermission $createPermissionsAction */
    $createPermissionsAction = $this->partialMock(CreatePermissions::class, function (MockInterface $mock) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createModelPermissions')
            ->andReturn();
    });

    $this->partialMock(AuthorizationPermissionRegistry::class, function (MockInterface $mock) {
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
    $this->assertDatabaseHas('permissions', [
        'name' => 'new-module.dashboard.access',
        'guard_name' => 'web',
    ]);

    $this->assertDatabaseHas('permissions', [
        'name' => 'new-module.dashboard.queries',
        'guard_name' => 'api',
    ]);

    expect(Permission::get())->toHaveCount(2);
});
