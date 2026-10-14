<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\Tenant;
use App\Queue\SqsOverflowStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('uses the landlord S3 root without a tenant', function () {
    $disk = app(SqsOverflowStorage::class)->disk(null);

    assert($disk instanceof FilesystemAdapter);

    expect($disk->getConfig())->toMatchArray([
        'bucket' => config('filesystems.disks.sqs-overflow.bucket'),
        'root' => config('filesystems.disks.sqs-overflow.root'),
    ]);
});

it('uses the S3 root of the tenant without making the tenant current', function () {
    $tenant = Tenant::query()->first();
    $s3Filesystem = $tenant->config->s3Filesystem;

    Tenant::forgetCurrent();

    $disk = app(SqsOverflowStorage::class)->disk($tenant->getKey());

    assert($disk instanceof FilesystemAdapter);

    expect($disk->getConfig())->toMatchArray([
        'driver' => 's3',
        'key' => $s3Filesystem->key,
        'region' => $s3Filesystem->region,
        'bucket' => $s3Filesystem->bucket,
        'root' => $s3Filesystem->root,
    ])
        ->and(Tenant::current())->toBeNull();
});

it('stores payloads under the `sqs-overflow` directory of the disk', function () {
    Storage::fake('sqs-overflow');

    $store = app(SqsOverflowStorage::class)->store(null);

    $store->put('laravel:sqs-payloads:probe', '{"uuid":"probe"}');

    expect(Storage::disk('sqs-overflow')->allFiles())->toHaveCount(1)
        ->and(Storage::disk('sqs-overflow')->allFiles()[0])->toStartWith('sqs-overflow/')
        ->and($store->get('laravel:sqs-payloads:probe'))->toBe('{"uuid":"probe"}');

    $store->forget('laravel:sqs-payloads:probe');

    expect(Storage::disk('sqs-overflow')->allFiles())->toBeEmpty();
});

it('has no disk or payloads for a tenant that no longer exists', function () {
    $missingTenantId = (string) Str::uuid();

    expect(app(SqsOverflowStorage::class)->disk($missingTenantId))->toBeNull()
        ->and(app(SqsOverflowStorage::class)->store($missingTenantId)->get('laravel:sqs-payloads:probe'))->toBeNull();
});
