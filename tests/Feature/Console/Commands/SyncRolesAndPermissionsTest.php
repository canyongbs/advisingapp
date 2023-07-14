<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SyncRolesAndPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncRolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_assign_permissions_to_roles_as_defined_in_our_configuration(): void
    {
        // Based on our configuration values
        Config::set('roles.web.admin', [
            'custom' => [],
            'model' => [
                'user' => [
                    '*.view',
                ],
            ],
        ]);

        // When we run the command
        Artisan::call(SyncRolesAndPermissions::class);

        // Our roles we have defined should have the corresponding permissions defined in configuration
        $role = Role::web()->firstWhere('name', 'admin');

        $this->assertTrue($role->hasPermissionTo('user.*.view'));
        $this->assertFalse($role->hasPermissionTo('user.*.update'));
    }
}
