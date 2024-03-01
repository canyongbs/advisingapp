<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'domain' => $this->faker->unique()->domainName,
            'key' => 'base64:' . base64_encode(
                Encrypter::generateKey(config('app.cipher'))
            ),
            'config' => $this::defaultConfig(),
        ];
    }

    public static function defaultConfig(): TenantConfig
    {
        return new TenantConfig(
            database: new TenantDatabaseConfig(
                host: config('database.connections.landlord.host'),
                port: config('database.connections.landlord.port'),
                database: 'test',
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
        );
    }
}
