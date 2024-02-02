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

namespace Tests;

use App\Models\Tenant;
use Tests\Concerns\LoadsFixtures;
use Illuminate\Contracts\Console\Kernel;
use App\Multitenancy\Actions\CreateTenant;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use Illuminate\Foundation\Testing\DatabaseTransactionsManager;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use App\Multitenancy\DataTransferObjects\TenantSisDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use AdvisingApp\Authorization\Console\Commands\SyncRolesAndPermissions;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use CanConfigureMigrationCommands;
    use LoadsFixtures;
    use UsesMultitenancyConfig;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Tenant::first()->makeCurrent();

        $this->beginDatabaseTransactionOnConnection($this->tenantDatabaseConnectionName());
        $this->beginDatabaseTransactionOnConnection('sis');
    }

    public function createTestingEnvironment(): void
    {
        $this->artisan('migrate:fresh', [
            '--database' => $this->landlordDatabaseConnectionName(),
            '--path' => 'database/migrations/landlord',
            ...$this->migrateFreshUsing(),
        ]);

        $tenant = $this->createTenant(
            name: 'Test Tenant',
            domain: 'test.advisingapp.local',
            database: 'testing_tenant',
            sisDatabase: 'testing',
        );

        $tenant->makeCurrent();

        $this->artisan('migrate:fresh', $this->migrateFreshUsing());

        $this->artisan('migrate:fresh', [
            '--database' => 'sis',
            '--path' => 'app-modules/student-data-model/database/migrations/sis',
            ...$this->migrateFreshUsing(),
        ]);

        $this->artisan('app:setup-foreign-data-wrapper');

        if (config('database.adm_materialized_views_enabled')) {
            $this->artisan('app:create-adm-materialized-views');
        }

        $currentTenant = Tenant::current();

        $this->artisan(
            command: SyncRolesAndPermissions::class,
            parameters: [
                '--tenant' => $currentTenant->id,
            ],
        );

        Tenant::forgetCurrent();

        $this->app[Kernel::class]->setArtisan(null);
    }

    public function beginDatabaseTransactionOnConnection(string $name)
    {
        $database = $this->app->make('db');

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager());

        $connection = $database->connection($name);
        $connection->setTransactionManager($transactionsManager);
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        $this->beforeApplicationDestroyed(function () use ($database, $name) {
            $connection = $database->connection($name);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollBack();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
    }

    public function createTenant(string $name, string $domain, string $database, string $sisDatabase): Tenant
    {
        return app(CreateTenant::class)(
            $name,
            $domain,
            new TenantConfig(
                database: new TenantDatabaseConfig(
                    host: config('database.connections.landlord.host'),
                    port: config('database.connections.landlord.port'),
                    database: $database,
                    username: config('database.connections.landlord.username'),
                    password: config('database.connections.landlord.password'),
                ),
                sisDatabase: new TenantSisDatabaseConfig(
                    host: config('database.connections.sis.host'),
                    port: config('database.connections.sis.port'),
                    database: $sisDatabase,
                    username: config('database.connections.sis.username'),
                    password: config('database.connections.sis.password'),
                ),
                s3Filesystem: new TenantS3FilesystemConfig(
                    key: config('filesystems.disks.s3.key'),
                    secret: config('filesystems.disks.s3.secret'),
                    region: config('filesystems.disks.s3.region'),
                    bucket: config('filesystems.disks.s3.bucket'),
                    url: config('filesystems.disks.s3.url'),
                    endpoint: config('filesystems.disks.s3.endpoint'),
                    usePathStyleEndpoint: config('filesystems.disks.s3.use_path_style_endpoint'),
                    throw: config('filesystems.disks.s3.throw'),
                    root: config('filesystems.disks.s3.root'),
                ),
                s3PublicFilesystem: new TenantS3FilesystemConfig(
                    key: config('filesystems.disks.s3-public.key'),
                    secret: config('filesystems.disks.s3-public.secret'),
                    region: config('filesystems.disks.s3-public.region'),
                    bucket: config('filesystems.disks.s3-public.bucket'),
                    url: config('filesystems.disks.s3-public.url'),
                    endpoint: config('filesystems.disks.s3-public.endpoint'),
                    usePathStyleEndpoint: config('filesystems.disks.s3-public.use_path_style_endpoint'),
                    throw: config('filesystems.disks.s3-public.throw'),
                    root: config('filesystems.disks.s3-public.root'),
                ),
                mail: new TenantMailConfig(
                    mailers: new TenantMailersConfig(
                        smtp: new TenantSmtpMailerConfig(
                            host: config('mail.mailers.smtp.host'),
                            port: config('mail.mailers.smtp.port'),
                            encryption: config('mail.mailers.smtp.encryption'),
                            username: config('mail.mailers.smtp.username'),
                            password: config('mail.mailers.smtp.password'),
                            timeout: config('mail.mailers.smtp.timeout'),
                            localDomain: config('mail.mailers.smtp.local_domain'),
                        )
                    ),
                    mailer: config('mail.default'),
                    fromAddress: config('mail.from.address'),
                    fromName: config('mail.from.name')
                ),
            )
        );
    }

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->createTestingEnvironment();

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransactionOnConnection($this->landlordDatabaseConnectionName());
    }
}
