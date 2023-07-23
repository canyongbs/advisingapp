<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use CanConfigureMigrationCommands;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->artisan('migrate:fresh', $this->migrateFreshUsing());

        $this->artisan('migrate:fresh', [
            '--database' => 'sis',
            '--path' => 'database/migrations/sis',
            ...$this->migrateFreshUsing(),
        ]);

        $this->artisan('app:setup-foreign-data-wrapper');

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
            $this->artisan('migrate:rollback', [
                '--database' => 'sis',
                '--path' => 'database/migrations/sis',
            ]);

            $this->artisan('app:remove-foreign-data-wrapper');

            RefreshDatabaseState::$migrated = false;
        });
    }
}
