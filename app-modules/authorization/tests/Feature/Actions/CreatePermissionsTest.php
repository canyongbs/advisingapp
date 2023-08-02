<?php

use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Config;
use App\Actions\Finders\ApplicationModels;
use Assist\Authorization\Models\Permission;
use Spatie\Permission\Commands\CreatePermission;
use Assist\Authorization\Actions\CreatePermissions;

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

    // Based on our configuration for custom permissions
    Config::set('permissions.web.custom', ['dashboard.access']);
    Config::set('permissions.api.custom', ['data.access']);

    // When we run the CreatePermissions action
    $createPermissionsAction->handle();

    // We should have created the records that were specified in config
    $this->assertDatabaseHas('permissions', [
        'name' => 'dashboard.access',
        'guard_name' => 'web',
    ]);

    $this->assertDatabaseHas('permissions', [
        'name' => 'data.access',
        'guard_name' => 'api',
    ]);

    expect(Permission::get())->toHaveCount(2);
});
