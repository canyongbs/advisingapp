<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the s3 storage configs', function () {
    $before = config()->getMany([
        'filesystems.disks.s3.key',
        'filesystems.disks.s3.secret',
        'filesystems.disks.s3.region',
        'filesystems.disks.s3.bucket',
        'filesystems.disks.s3.url',
        'filesystems.disks.s3.endpoint',
        'filesystems.disks.s3.use_path_style_endpoint',
        'filesystems.disks.s3.throw',
        'filesystems.disks.s3.root',
    ]);

    $tenant = Tenant::first()->makeCurrent();

    $after = config()->getMany([
        'filesystems.disks.s3.key',
        'filesystems.disks.s3.secret',
        'filesystems.disks.s3.region',
        'filesystems.disks.s3.bucket',
        'filesystems.disks.s3.url',
        'filesystems.disks.s3.endpoint',
        'filesystems.disks.s3.use_path_style_endpoint',
        'filesystems.disks.s3.throw',
        'filesystems.disks.s3.root',
    ]);

    assertEquals($after, [
        'filesystems.disks.s3.key' => $tenant->config->s3Filesystem->key,
        'filesystems.disks.s3.secret' => $tenant->config->s3Filesystem->secret,
        'filesystems.disks.s3.region' => $tenant->config->s3Filesystem->region,
        'filesystems.disks.s3.bucket' => $tenant->config->s3Filesystem->bucket,
        'filesystems.disks.s3.url' => $tenant->config->s3Filesystem->url,
        'filesystems.disks.s3.endpoint' => $tenant->config->s3Filesystem->endpoint,
        'filesystems.disks.s3.use_path_style_endpoint' => $tenant->config->s3Filesystem->usePathStyleEndpoint,
        'filesystems.disks.s3.throw' => $tenant->config->s3Filesystem->throw,
        'filesystems.disks.s3.root' => $tenant->config->s3Filesystem->root,
    ]);

    Tenant::forgetCurrent();

    $after = config()->get([
        'filesystems.disks.s3.key',
        'filesystems.disks.s3.secret',
        'filesystems.disks.s3.region',
        'filesystems.disks.s3.bucket',
        'filesystems.disks.s3.url',
        'filesystems.disks.s3.endpoint',
        'filesystems.disks.s3.use_path_style_endpoint',
        'filesystems.disks.s3.throw',
        'filesystems.disks.s3.root',
    ]);

    assertEquals($before, $after);
});
