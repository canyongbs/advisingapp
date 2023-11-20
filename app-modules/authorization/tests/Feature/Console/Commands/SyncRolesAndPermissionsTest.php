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

use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Config;
use Assist\Authorization\Tests\Helpers;
use Illuminate\Support\Facades\Artisan;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;

beforeEach(function () {
    (new Helpers())->truncateTables();
});

it('will assign permissions to roles as defined in our configuration', function () {
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
})->skip();
