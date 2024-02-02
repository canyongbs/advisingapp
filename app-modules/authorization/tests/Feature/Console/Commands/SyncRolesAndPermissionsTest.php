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

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Tests\Helpers;
use AdvisingApp\Authorization\Console\Commands\SyncRolesAndPermissions;

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

    $currentTenant = Tenant::current();

    // When we run the SyncRolesAndPermissions command
    Artisan::call(
        command: SyncRolesAndPermissions::class,
        parameters: [
            "--tenant={$currentTenant->id}",
        ],
    );

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
