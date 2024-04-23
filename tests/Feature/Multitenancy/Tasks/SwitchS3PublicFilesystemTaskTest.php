<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the s3 storage configs', function () {
    $before = config()->getMany([
        'filesystems.disks.s3-public.key',
        'filesystems.disks.s3-public.secret',
        'filesystems.disks.s3-public.region',
        'filesystems.disks.s3-public.bucket',
        'filesystems.disks.s3-public.url',
        'filesystems.disks.s3-public.endpoint',
        'filesystems.disks.s3-public.use_path_style_endpoint',
        'filesystems.disks.s3-public.throw',
        'filesystems.disks.s3-public.root',
    ]);

    $tenant = Tenant::first()->makeCurrent();

    $after = config()->getMany([
        'filesystems.disks.s3-public.key',
        'filesystems.disks.s3-public.secret',
        'filesystems.disks.s3-public.region',
        'filesystems.disks.s3-public.bucket',
        'filesystems.disks.s3-public.url',
        'filesystems.disks.s3-public.endpoint',
        'filesystems.disks.s3-public.use_path_style_endpoint',
        'filesystems.disks.s3-public.throw',
        'filesystems.disks.s3-public.root',
    ]);

    assertEquals($after, [
        'filesystems.disks.s3-public.key' => $tenant->config->s3PublicFilesystem->key,
        'filesystems.disks.s3-public.secret' => $tenant->config->s3PublicFilesystem->secret,
        'filesystems.disks.s3-public.region' => $tenant->config->s3PublicFilesystem->region,
        'filesystems.disks.s3-public.bucket' => $tenant->config->s3PublicFilesystem->bucket,
        'filesystems.disks.s3-public.url' => $tenant->config->s3PublicFilesystem->url,
        'filesystems.disks.s3-public.endpoint' => $tenant->config->s3PublicFilesystem->endpoint,
        'filesystems.disks.s3-public.use_path_style_endpoint' => $tenant->config->s3PublicFilesystem->usePathStyleEndpoint,
        'filesystems.disks.s3-public.throw' => $tenant->config->s3PublicFilesystem->throw,
        'filesystems.disks.s3-public.root' => $tenant->config->s3PublicFilesystem->root,
    ]);

    Tenant::forgetCurrent();

    $after = config()->get([
        'filesystems.disks.s3-public.key',
        'filesystems.disks.s3-public.secret',
        'filesystems.disks.s3-public.region',
        'filesystems.disks.s3-public.bucket',
        'filesystems.disks.s3-public.url',
        'filesystems.disks.s3-public.endpoint',
        'filesystems.disks.s3-public.use_path_style_endpoint',
        'filesystems.disks.s3-public.throw',
        'filesystems.disks.s3-public.root',
    ]);

    assertEquals($before, $after);
});
