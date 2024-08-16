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

namespace App\Http\Controllers\Tenants;

use Sqids\Sqids;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Multitenancy\Actions\CreateTenant;
use App\Http\Requests\Tenants\CreateTenantRequest;
use App\Multitenancy\DataTransferObjects\TenantUser;
use AdvisingApp\Theme\DataTransferObjects\ThemeConfig;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use App\Multitenancy\DataTransferObjects\TenantMailersConfig;
use App\Multitenancy\DataTransferObjects\TenantDatabaseConfig;
use App\DataTransferObjects\LicenseManagement\LicenseAddonsData;
use App\DataTransferObjects\LicenseManagement\LicenseLimitsData;
use App\Multitenancy\DataTransferObjects\TenantSmtpMailerConfig;
use App\Multitenancy\DataTransferObjects\TenantS3FilesystemConfig;
use App\DataTransferObjects\LicenseManagement\LicenseSubscriptionData;

class CreateTenantController
{
    public function __invoke(CreateTenantRequest $request): JsonResponse
    {
        $name = $request->validated('name');
        $rootName = Str::snake($name) . '_' . (new Sqids())->encode([time()]);

        $tenant = app(CreateTenant::class)(
            $name,
            $request->validated('domain'),
            new TenantConfig(
                database: new TenantDatabaseConfig(
                    host: config('database.connections.landlord.host'),
                    port: config('database.connections.landlord.port'),
                    database: $request->validated('database'),
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
                    root: $rootName,
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
                    root: $rootName . '/PUBLIC',
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
            ),
            new TenantUser(
                name: $request->validated('user.name'),
                email: $request->validated('user.email'),
                password: $request->validated('user.password'),
            ),
            new LicenseData(
                updatedAt: now(),
                subscription: LicenseSubscriptionData::from($request->validated('subscription')),
                limits: LicenseLimitsData::from($request->validated('limits')),
                addons: LicenseAddonsData::from($request->validated('addons')),
            ),
            new ThemeConfig(
                colorOverrides: $request->validated('theme.color_overrides') ?? [],
                hasDarkMode: $request->validated('theme.has_dark_mode') ?? true,
                url: $request->validated('theme.url'),
            ),
        );

        return response()->json(['tenant' => [
            'id' => $tenant->getKey(),
        ]]);
    }
}
