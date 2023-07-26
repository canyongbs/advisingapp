<?php

namespace Tests\Feature\Actions\RolesAndPermissions;

use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Config;
use App\Actions\Finders\ApplicationModels;
use Assist\Authorization\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Assist\Authorization\Actions\CreatePermissions;
use App\Actions\RolesAndPermissions\CreatePermissions;

class CreatePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_create_appropriate_permissions_for_all_models(): void
    {
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
    }

    /** @test */
    public function it_will_create_appropriate_custom_permissions(): void
    {
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

        $this->assertCount(2, Permission::get());
    }
}
