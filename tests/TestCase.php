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

namespace Tests;

use Tests\Concerns\LoadsFixtures;
use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Assist\Authorization\Console\Commands\SyncRolesAndPermissions;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use CanConfigureMigrationCommands;
    use LoadsFixtures;

    protected array $connectionsToTransact = ['pgsql', 'sis'];

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function createTestingEnvironment(): void
    {
        $this->artisan('migrate:fresh', $this->migrateFreshUsing());

        $this->artisan('migrate:fresh', [
            '--database' => 'sis',
            '--path' => 'app-modules/assist-data-model/database/migrations/sis',
            ...$this->migrateFreshUsing(),
        ]);

        $this->artisan('app:remove-foreign-data-wrapper');

        $this->artisan('app:setup-foreign-data-wrapper');

        if (config('database.adm_materialized_views_enabled')) {
            $this->artisan('app:create-adm-materialized-views');
        }

        $this->artisan(SyncRolesAndPermissions::class);

        $this->app[Kernel::class]->setArtisan(null);
    }

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->createTestingEnvironment();

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
