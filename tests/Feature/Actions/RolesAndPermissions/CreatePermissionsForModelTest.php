<?php

namespace Tests\Feature\Actions\RolesAndPermissions;

use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Actions\RolesAndPermissions\CreatePermissionsForModel;

class CreatePermissionsForModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Relation::morphMap([
            'user' => \Mockery_3_App_Models_User::class,
        ]);
    }

    /** @test */
    public function it_will_respect_model_permission_overrides(): void
    {
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
    }

    /** @test */
    public function it_will_respect_model_permission_extensions(): void
    {
        // When a model extends the default configuration for permissions
        $this->partialMock(User::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('getWebPermissions')
                ->once()
                ->andReturn(collect(['*.test', ...config('permissions.web.model')]));
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
    }
}
