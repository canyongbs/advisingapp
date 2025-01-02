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

namespace App\Multitenancy\Tasks;

use App\Multitenancy\DataTransferObjects\TenantConfig;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchS3PublicFilesystemTask implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalKey = null,
        protected ?string $originalSecret = null,
        protected ?string $originalRegion = null,
        protected ?string $originalBucket = null,
        protected ?string $originalUrl = null,
        protected ?string $originalEndpoint = null,
        protected bool $originalUsePathStyleEndpoint = false,
        protected bool $originalThrow = false,
        protected ?string $originalRoot = null,
    ) {
        $this->originalKey ??= config('filesystems.disks.s3-public.key');
        $this->originalSecret ??= config('filesystems.disks.s3-public.secret');
        $this->originalRegion ??= config('filesystems.disks.s3-public.region');
        $this->originalBucket ??= config('filesystems.disks.s3-public.bucket');
        $this->originalUrl ??= config('filesystems.disks.s3-public.url');
        $this->originalEndpoint ??= config('filesystems.disks.s3-public.endpoint');
        $this->originalUsePathStyleEndpoint ??= config('filesystems.disks.s3-public.use_path_style_endpoint');
        $this->originalThrow ??= config('filesystems.disks.s3-public.throw');
        $this->originalRoot ??= config('filesystems.disks.s3-public.root');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        /** @var TenantConfig $config */
        $config = $tenant->config;

        $this->setFilesystemConfig(
            key: $config->s3PublicFilesystem->key,
            secret: $config->s3PublicFilesystem->secret,
            region: $config->s3PublicFilesystem->region,
            bucket: $config->s3PublicFilesystem->bucket,
            url: $config->s3PublicFilesystem->url,
            endpoint: $config->s3PublicFilesystem->endpoint,
            usePathStyleEndpoint: $config->s3PublicFilesystem->usePathStyleEndpoint,
            throw: $config->s3PublicFilesystem->throw,
            root: $config->s3PublicFilesystem->root,
        );
    }

    public function forgetCurrent(): void
    {
        $this->setFilesystemConfig(
            key: $this->originalKey,
            secret: $this->originalSecret,
            region: $this->originalRegion,
            bucket: $this->originalBucket,
            url: $this->originalUrl,
            endpoint: $this->originalEndpoint,
            usePathStyleEndpoint: $this->originalUsePathStyleEndpoint,
            throw: $this->originalThrow,
            root: $this->originalRoot,
        );
    }

    protected function setFilesystemConfig(
        ?string $key,
        ?string $secret,
        ?string $region,
        ?string $bucket,
        ?string $url,
        ?string $endpoint,
        bool $usePathStyleEndpoint,
        bool $throw,
        ?string $root,
    ): void {
        config([
            'filesystems.disks.s3-public.key' => $key,
            'filesystems.disks.s3-public.secret' => $secret,
            'filesystems.disks.s3-public.region' => $region,
            'filesystems.disks.s3-public.bucket' => $bucket,
            'filesystems.disks.s3-public.url' => $url,
            'filesystems.disks.s3-public.endpoint' => $endpoint,
            'filesystems.disks.s3-public.use_path_style_endpoint' => $usePathStyleEndpoint,
            'filesystems.disks.s3-public.throw' => $throw,
            'filesystems.disks.s3-public.root' => $root,
        ]);

        Storage::forgetDisk('s3-public');
    }
}
