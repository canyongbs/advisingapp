<?php

namespace Assist\Authorization\Tests\Feature\Console\Commands;

use Tests\TestCase;
use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;

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

        Config::set('permissions.api.custom', [
            'export_reports',
        ]);

        Config::set('roles.api.admin', [
            'custom' => ['export_reports'],
            'model' => [],
        ]);

        // When we run the SyncRolesAndPermissions command
        Artisan::call(SyncRolesAndPermissions::class);

        // Our roles we have defined should have attached the corresponding permissions defined in configuration
        $webAdmin = Role::web()->firstWhere('name', 'admin');

        $this->assertTrue($webAdmin->hasPermissionTo('user.*.view'));
        $this->assertFalse($webAdmin->hasPermissionTo('user.*.update'));
        $this->assertFalse($webAdmin->hasPermissionTo('export_reports'));

        // Our roles we have defined should have attached the corresponding permissions defined in configuration
        $apiAdmin = Role::api()->firstWhere('name', 'admin');

        $this->assertTrue($apiAdmin->hasPermissionTo('export_reports'));
        $this->assertFalse($apiAdmin->hasPermissionTo('user.*.view'));
    }
}
