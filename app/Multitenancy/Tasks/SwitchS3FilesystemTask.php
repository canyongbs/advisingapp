<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use App\Multitenancy\DataTransferObjects\TenantConfig;

class SwitchS3FilesystemTask implements SwitchTenantTask
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
        $this->originalKey ??= config('filesystems.disks.s3.key');
        $this->originalSecret ??= config('filesystems.disks.s3.secret');
        $this->originalRegion ??= config('filesystems.disks.s3.region');
        $this->originalBucket ??= config('filesystems.disks.s3.bucket');
        $this->originalUrl ??= config('filesystems.disks.s3.url');
        $this->originalEndpoint ??= config('filesystems.disks.s3.endpoint');
        $this->originalUsePathStyleEndpoint ??= config('filesystems.disks.s3.use_path_style_endpoint');
        $this->originalThrow ??= config('filesystems.disks.s3.throw');
        $this->originalRoot ??= config('filesystems.disks.s3.root');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        /** @var TenantConfig $config */
        $config = $tenant->config;

        $this->setFilesystemConfig(
            key: $config->s3Filesystem->key,
            secret: $config->s3Filesystem->secret,
            region: $config->s3Filesystem->region,
            bucket: $config->s3Filesystem->bucket,
            url: $config->s3Filesystem->url,
            endpoint: $config->s3Filesystem->endpoint,
            usePathStyleEndpoint: $config->s3Filesystem->usePathStyleEndpoint,
            throw: $config->s3Filesystem->throw,
            root: $config->s3Filesystem->root,
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
            'filesystems.disks.s3.key' => $key,
            'filesystems.disks.s3.secret' => $secret,
            'filesystems.disks.s3.region' => $region,
            'filesystems.disks.s3.bucket' => $bucket,
            'filesystems.disks.s3.url' => $url,
            'filesystems.disks.s3.endpoint' => $endpoint,
            'filesystems.disks.s3.use_path_style_endpoint' => $usePathStyleEndpoint,
            'filesystems.disks.s3.throw' => $throw,
            'filesystems.disks.s3.root' => $root,
        ]);
    }
}
