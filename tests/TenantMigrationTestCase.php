<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

abstract class TenantMigrationTestCase extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::first()->makeCurrent();
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations(): void
    {
        $this->beforeRefreshingDatabase();
        $this->refreshTestDatabase();
        $this->afterRefreshingDatabase();

        $this->beforeApplicationDestroyed(function () {
            $this->refreshTenantTestingEnvironment();
        });
    }

    protected function refreshTestDatabase(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->createLandlordTestingEnvironment();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $beginLandlordTransaction = function () {
            $this->beginDatabaseTransactionOnConnection($this->landlordDatabaseConnectionName());
        };

        if (! in_array($beginLandlordTransaction, $this->afterApplicationCreatedCallbacks)) {
            $this->afterApplicationCreated($beginLandlordTransaction);
        }
    }
}
