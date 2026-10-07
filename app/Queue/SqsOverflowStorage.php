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

namespace App\Queue;

use App\Models\Tenant;
use Illuminate\Cache\NullStore;
use Illuminate\Cache\StorageStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Keeps the payloads of SQS messages too large to send under the S3 root of the tenant that dispatched them, or the
 * landlord's. The disk is built from the tenant's settings without making the tenant current, so a worker can read a
 * payload before it knows which tenant the job is for.
 */
class SqsOverflowStorage
{
    /**
     * Returns null when the tenant no longer exists.
     */
    public function disk(?string $tenantId): ?Filesystem
    {
        if ($tenantId === null) {
            return Storage::disk('sqs-overflow');
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            return null;
        }

        $s3Filesystem = $tenant->config->s3Filesystem;

        return Storage::build([
            ...config('filesystems.disks.sqs-overflow'),
            'key' => $s3Filesystem->key,
            'secret' => $s3Filesystem->secret,
            'region' => $s3Filesystem->region,
            'bucket' => $s3Filesystem->bucket,
            'endpoint' => $s3Filesystem->endpoint,
            'use_path_style_endpoint' => $s3Filesystem->usePathStyleEndpoint,
            'throw' => $s3Filesystem->throw,
            'root' => $s3Filesystem->root,
        ]);
    }

    public function store(?string $tenantId): Repository
    {
        $disk = $this->disk($tenantId);

        return Cache::repository($disk ? new StorageStore($disk, 'sqs-overflow') : new NullStore());
    }
}
