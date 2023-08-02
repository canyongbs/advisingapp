<?php

use Illuminate\Support\Facades\DB;
use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;

beforeEach(function () {
    DB::table('roles')->truncate();
    DB::table('permissions')->truncate();
    DB::table('role_groups')->truncate();
    DB::table('role_groupables')->truncate();
    DB::table('model_has_roles')->truncate();
    DB::table('role_has_permissions')->truncate();
    DB::table('model_has_permissions')->truncate();
});

it('will assign permissions to roles as defined in our configuration', function () {
    $this->markTestSkipped();

    // TODO This test needs to be fixed once we determine exactly how we are creating
    // and syncing roles and permissions through each module that introduces them.
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

    expect($webAdmin->hasPermissionTo('user.*.view'))->toBeTrue();
    expect($webAdmin->hasPermissionTo('user.*.update'))->toBeFalse();
    expect($webAdmin->hasPermissionTo('export_reports'))->toBeFalse();

    // Our roles we have defined should have attached the corresponding permissions defined in configuration
    $apiAdmin = Role::api()->firstWhere('name', 'admin');

    expect($apiAdmin->hasPermissionTo('export_reports'))->toBeTrue();
    expect($apiAdmin->hasPermissionTo('user.*.view'))->toBeFalse();
});
