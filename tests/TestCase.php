<?php

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
