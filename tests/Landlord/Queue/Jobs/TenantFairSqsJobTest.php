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
use App\Queue\Jobs\TenantFairSqsJob;
use App\Queue\SqsOverflowStorage;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function tenantFairSqsJobWithBody(string $body, ?SqsClient $sqs = null): TenantFairSqsJob
{
    $sqs ??= Mockery::mock(SqsClient::class);

    assert($sqs instanceof SqsClient);

    return new TenantFairSqsJob(
        app(),
        $sqs,
        [
            'MessageId' => 'message-id',
            'ReceiptHandle' => 'receipt-handle',
            'Body' => $body,
            'Attributes' => ['ApproximateReceiveCount' => '1'],
        ],
        'sqs',
        'https://sqs.us-east-1.amazonaws.com/123456789012/default',
        config('queue.connections.sqs.overflow'),
    );
}

function sqsClientExpectingDeletion(): SqsClient
{
    $sqs = Mockery::mock(SqsClient::class);
    $sqs->shouldReceive('deleteMessage')->once()->andReturn(new Result([]));

    assert($sqs instanceof SqsClient);

    return $sqs;
}

/**
 * Serves the given disk for one tenant only, as a tenant's S3 root cannot be faked through the `Storage` facade.
 */
function sqsOverflowStorageWithTenantDisk(string $tenantId, Filesystem $disk): void
{
    app()->instance(SqsOverflowStorage::class, new class ($tenantId, $disk) extends SqsOverflowStorage {
        public function __construct(
            private string $tenantId,
            private Filesystem $tenantDisk,
        ) {}

        public function disk(?string $tenantId): ?Filesystem
        {
            return $tenantId === $this->tenantId ? $this->tenantDisk : parent::disk($tenantId);
        }
    });
}

it('reads an inline payload from the message body', function () {
    $job = tenantFairSqsJobWithBody('{"uuid":"inline"}');

    expect($job->getRawBody())->toBe('{"uuid":"inline"}');
});

it('reads an offloaded payload from the S3 root of the tenant named in the message', function () {
    $tenant = Tenant::query()->first();

    $tenantDisk = Storage::fake('tenant-sqs-overflow');
    Storage::fake('sqs-overflow');

    sqsOverflowStorageWithTenantDisk($tenant->getKey(), $tenantDisk);

    $pointer = 'laravel:sqs-payloads:' . Str::uuid();

    app(SqsOverflowStorage::class)->store($tenant->getKey())->put($pointer, '{"uuid":"offloaded"}');

    $job = tenantFairSqsJobWithBody(json_encode(['@pointer' => $pointer, 'tenantId' => $tenant->getKey()]));

    expect($job->getRawBody())->toBe('{"uuid":"offloaded"}')
        ->and(Storage::disk('sqs-overflow')->allFiles())->toBeEmpty();
});

it('deletes the message and throws when its offloaded payload is missing', function () {
    Storage::fake('sqs-overflow');

    $job = tenantFairSqsJobWithBody(
        json_encode(['@pointer' => 'laravel:sqs-payloads:' . Str::uuid()]),
        sqsClientExpectingDeletion(),
    );

    expect(fn () => $job->getRawBody())->toThrow(RuntimeException::class)
        ->and($job->isDeleted())->toBeTrue();
});

it('deletes the message and throws when the tenant named in the message no longer exists', function () {
    $job = tenantFairSqsJobWithBody(
        json_encode([
            '@pointer' => 'laravel:sqs-payloads:' . Str::uuid(),
            'tenantId' => (string) Str::uuid(),
        ]),
        sqsClientExpectingDeletion(),
    );

    expect(fn () => $job->getRawBody())->toThrow(RuntimeException::class)
        ->and($job->isDeleted())->toBeTrue();
});

describe('legacy pointer messages', function () {
    it('reads a landlord payload from the landlord S3 root', function () {
        Storage::fake('sqs-overflow');
        Storage::disk('sqs-overflow')->put('sqs-payloads/legacy.json', '{"uuid":"legacy"}');

        $job = tenantFairSqsJobWithBody(json_encode(['pointer' => 'sqs-payloads/legacy.json']));

        expect($job->getRawBody())->toBe('{"uuid":"legacy"}');
    });

    it('reads a tenant payload from the S3 root of that tenant', function () {
        $tenant = Tenant::query()->first();

        $tenantDisk = Storage::fake('tenant-sqs-overflow');
        $tenantDisk->put('sqs-payloads/legacy.json', '{"uuid":"legacy"}');

        Storage::fake('sqs-overflow');

        sqsOverflowStorageWithTenantDisk($tenant->getKey(), $tenantDisk);

        $job = tenantFairSqsJobWithBody(json_encode([
            'pointer' => 'sqs-payloads/legacy.json',
            'tenantId' => $tenant->getKey(),
        ]));

        expect($job->getRawBody())->toBe('{"uuid":"legacy"}');
    });

    it('deletes the payload file when the job is deleted', function () {
        Storage::fake('sqs-overflow');
        Storage::disk('sqs-overflow')->put('sqs-payloads/legacy.json', '{"uuid":"legacy"}');

        $job = tenantFairSqsJobWithBody(
            json_encode(['pointer' => 'sqs-payloads/legacy.json']),
            sqsClientExpectingDeletion(),
        );

        Storage::disk('sqs-overflow')->assertExists('sqs-payloads/legacy.json');

        $job->delete();

        Storage::disk('sqs-overflow')->assertMissing('sqs-payloads/legacy.json');
    });

    it('deletes the message and throws when its tenant no longer exists', function () {
        $job = tenantFairSqsJobWithBody(
            json_encode([
                'pointer' => 'sqs-payloads/legacy.json',
                'tenantId' => (string) Str::uuid(),
            ]),
            sqsClientExpectingDeletion(),
        );

        expect(fn () => $job->getRawBody())->toThrow(RuntimeException::class)
            ->and($job->isDeleted())->toBeTrue();
    });
});
