<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Console\Commands\SetupRoles;
use App\DataTransferObjects\LicenseManagement\LicenseAddonsData;
use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\DataTransferObjects\LicenseManagement\LicenseLimitsData;
use App\DataTransferObjects\LicenseManagement\LicenseSubscriptionData;
use App\Jobs\UpdateTenantLicenseData;
use App\Models\Tenant;
use App\Multitenancy\Actions\CreateTenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use Illuminate\Foundation\Testing\DatabaseTransactionsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\LoadsFixtures;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use CanConfigureMigrationCommands;
    use LoadsFixtures;
    use UsesMultitenancyConfig;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function createLandlordTestingEnvironment(): void
    {
        $this->artisan('migrate:fresh', [
            '--database' => $this->landlordDatabaseConnectionName(),
            '--path' => 'database/landlord',
            ...$this->migrateFreshUsing(),
        ]);

        $tenantDatabase = ParallelTesting::token() ? 'testing_tenant_test_' . ParallelTesting::token() : 'testing_tenant';

        $this->createTenant(
            name: 'Test Tenant',
            domain: 'test.advisingapp.local',
            database: $tenantDatabase,
        );
    }

    protected function refreshTenantTestingEnvironment(?Tenant $tenant = null): void
    {
        $tenant ??= Tenant::firstOrFail();

        $tenant->execute(function () use ($tenant) {
            $this->artisan('migrate:fresh', [
                '--database' => $this->tenantDatabaseConnectionName(),
                ...$this->migrateFreshUsing(),
            ]);

            Artisan::call(
                command: SetupRoles::class,
                parameters: [
                    '--tenant' => $tenant->id,
                ],
            );

            dispatch_sync(new UpdateTenantLicenseData(
                $tenant,
                new LicenseData(
                    updatedAt: now(),
                    subscription: new LicenseSubscriptionData(
                        clientName: 'Jane Smith',
                        partnerName: 'Fake Edu Tech',
                        startDate: now(),
                        endDate: now()->addYear(),
                    ),
                    limits: new LicenseLimitsData(
                        conversationalAiSeats: 50,
                        conversationalAiAssistants: 10,
                        retentionCrmSeats: 25,
                        recruitmentCrmSeats: 10,
                        emails: 1000,
                        sms: 1000,
                        resetDate: now()->format('m-d'),
                    ),
                    addons: new LicenseAddonsData(
                        onlineForms: true,
                        onlineSurveys: true,
                        onlineAdmissions: true,
                        caseManagement: true,
                        resourceHub: true,
                        supportPrograms: true,
                        eventManagement: true,
                        realtimeChat: true,
                        mobileApps: true,
                        scheduleAndAppointments: true,
                    )
                )
            ));
        });
    }

    protected function beginDatabaseTransactionOnConnection(string $name): void
    {
        $database = $this->app->make('db');

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager([$name]));

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

    protected function createTenant(string $name, string $domain, string $database): Tenant
    {
        $tenant = app(CreateTenant::class)(
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
                    isDemoModeEnabled: false,
                    fromName: config('mail.from.name')
                ),
            ),
            null,
            new LicenseData(
                updatedAt: now(),
                subscription: new LicenseSubscriptionData(
                    clientName: 'Jane Smith',
                    partnerName: 'Fake Edu Tech',
                    startDate: now(),
                    endDate: now()->addYear(),
                ),
                limits: new LicenseLimitsData(
                    conversationalAiSeats: 50,
                    conversationalAiAssistants: 10,
                    retentionCrmSeats: 25,
                    recruitmentCrmSeats: 10,
                    emails: 1000,
                    sms: 1000,
                    resetDate: now()->format('m-d'),
                ),
                addons: new LicenseAddonsData(
                    onlineForms: true,
                    onlineSurveys: true,
                    onlineAdmissions: true,
                    caseManagement: true,
                    resourceHub: true,
                    supportPrograms: true,
                    eventManagement: true,
                    realtimeChat: true,
                    mobileApps: true,
                    scheduleAndAppointments: true,
                ),
            ),
            null,
            false
        );

        Artisan::call(
            command: SetupRoles::class,
            parameters: [
                '--tenant' => $tenant->id,
            ],
        );

        return $tenant;
    }
}
