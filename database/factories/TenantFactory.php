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

namespace Database\Factories;

use App\Models\Tenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Encryption\Encrypter;

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
        );
    }
}
