<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use App\Multitenancy\DataTransferObjects\TenantConfig;

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
    }
}
