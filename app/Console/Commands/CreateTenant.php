<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Console\Commands;

use Sqids\Sqids;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\DataTransferObjects\LicenseManagement\LicenseAddonsData;
use App\DataTransferObjects\LicenseManagement\LicenseLimitsData;
use App\Multitenancy\Actions\CreateTenant as CreateTenantAction;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use App\DataTransferObjects\LicenseManagement\LicenseSubscriptionData;

class CreateTenant extends Command
{
    protected $signature = 'tenants:create {name} {domain} {--m|run-queue} {--s|seed} {--a|admin} {--l|local} {--y|yes}';

    protected $description = 'Temporary command to test the tenant creation process.';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('This command cannot be run in production.');

            return static::FAILURE;
        }

        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $database = str($domain)
            ->replace(['.', '-'], '_')
            ->toString();
        $rootName = Str::snake($name) . '_' . (new Sqids())->encode([time()]);

        DB::connection('landlord')->statement("DROP DATABASE IF EXISTS {$database}");
        DB::connection('landlord')->statement("CREATE DATABASE {$database}");

        Tenant::where('domain', $domain)->delete();

        $tenant = app(CreateTenantAction::class)(
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
                    root: trim(rtrim(config('filesystems.disks.s3.root'), '/') . "/{$rootName}", '/'),
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
                    root: trim(rtrim(config('filesystems.disks.s3.root'), '/') . "/{$rootName}/PUBLIC", '/'),
                ),
                mail: new TenantMailConfig(
                    isDemoModeEnabled: false,
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
            ),
            licenseData: new LicenseData(
                updatedAt: now(),
                subscription: new LicenseSubscriptionData(
                    clientName: 'Jane Smith',
                    partnerName: 'Fake Edu Tech',
                    clientPo: 'abc123',
                    partnerPo: 'def456',
                    startDate: now(),
                    endDate: now()->addYear(),
                ),
                limits: new LicenseLimitsData(
                    conversationalAiSeats: 50,
                    conversationalAiAssistants: 0,
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
                    serviceManagement: true,
                    knowledgeManagement: true,
                    eventManagement: true,
                    realtimeChat: true,
                    mobileApps: true,
                    scheduleAndAppointments: true,
                ),
            ),
        );

        if ($this->option('no-interaction')) {
            return static::SUCCESS;
        }

        $database = config('multitenancy.tenant_database_connection_name');

        if ($this->option('yes') || $this->option('run-queue') || $this->confirm('Run the queue to migrate tenant databases?')) {
            $queue = config('queue.landlord_queue');

            Artisan::call(
                command: "queue:work --queue={$queue} --stop-when-empty",
                outputBuffer: $this->output,
            );
        }

        if ($this->option('yes') || $this->option('seed') || $this->confirm('Seed students in the tenant database?')) {
            Artisan::call(
                command: "tenants:artisan \"db:seed --database={$database} --class=StudentSeeder\" --tenant={$tenant->id}",
                outputBuffer: $this->output,
            );
        }

        if ($this->option('yes') || $this->option('admin') || $this->confirm('Would you like to seed sample super admin?')) {
            Artisan::call(
                command: "tenants:artisan \"db:seed --database={$database} --class=SampleSuperAdminUserSeeder\" --tenant={$tenant->id}",
                outputBuffer: $this->output,
            );
        }

        if ($this->option('yes') || $this->option('local') || $this->confirm('Would you like to seed local development data?')) {
            Artisan::call(
                command: "tenants:artisan \"db:seed --database={$database} --class=LocalDevelopmentSeeder\" --tenant={$tenant->id}",
                outputBuffer: $this->output,
            );
        }

        return static::SUCCESS;
    }
}
