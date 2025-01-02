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
