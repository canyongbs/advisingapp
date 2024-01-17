<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Multitenancy\Actions\CreateTenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use App\Multitenancy\DataTransferObjects\TenantSisDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenants:create {name} {domain}';

    protected $description = 'Temporary command to test the tenant creation process.';

    public function handle(): void
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');

        $database = 'tenant_' . strtolower(Str::random(30));

        DB::connection('landlord')->statement("CREATE DATABASE {$database}");

        $sisDatabase = 'tenant_' . strtolower(Str::random(30));

        DB::connection('sis')->statement("CREATE DATABASE {$sisDatabase}");

        app(CreateTenant::class)(
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
                ),
                s3PublicFilesystem: new TenantS3FilesystemConfig(
                    key: config('filesystems.disks.s3_public.key'),
                    secret: config('filesystems.disks.s3_public.secret'),
                    region: config('filesystems.disks.s3_public.region'),
                    bucket: config('filesystems.disks.s3_public.bucket'),
                ),
                mail: new TenantMailConfig(
                    mailers: new TenantMailersConfig(
                        smtp: new TenantSmtpMailerConfig(
                            host: config('mail.mailers.smtp.host'),
                            port: config('mail.mailers.smtp.port'),
                            encryption: config('mail.mailers.smtp.encryption'),
                            username: config('mail.mailers.smtp.username'),
                            password: config('mail.mailers.smtp.password'),
                        )
                    ),
                    mailer: config('mail.default'),
                    fromAddress: config('mail.from.address'),
                    fromName: config('mail.from.name')
                ),
            )
        );
    }
}
