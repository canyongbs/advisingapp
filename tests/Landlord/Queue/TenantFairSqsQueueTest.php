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
use App\Queue\TenantFairSqsQueue;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Illuminate\Cache\ArrayStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Builds the queue as the `sqs` connection is configured, on an SQS client that records what is sent and deleted, and
 * hands the last sent message back when the queue is popped.
 *
 * @param array<int, array<string, mixed>> $sentMessages
 * @param array<int, array<string, mixed>> $deletedMessages
 * @param array<int, array<string, mixed>> $releasedMessages
 */
function tenantFairSqsQueueRecordingInto(array &$sentMessages, array &$deletedMessages = [], array &$releasedMessages = []): QueueContract
{
    $sqs = Mockery::mock(SqsClient::class);

    $sqs->shouldReceive('sendMessage')->andReturnUsing(function (array $message) use (&$sentMessages): Result {
        $sentMessages[] = $message;

        return new Result(['MessageId' => 'message-' . count($sentMessages)]);
    });

    $sqs->shouldReceive('sendMessageBatch')->andReturnUsing(function (array $batch) use (&$sentMessages): Result {
        array_push($sentMessages, ...$batch['Entries']);

        return new Result([
            'Successful' => array_map(
                fn (array $entry): array => ['Id' => $entry['Id'], 'MessageId' => "message-{$entry['Id']}"],
                $batch['Entries'],
            ),
        ]);
    });

    $sqs->shouldReceive('receiveMessage')->andReturnUsing(function () use (&$sentMessages): Result {
        $lastSentMessage = end($sentMessages);

        return new Result([
            'Messages' => $lastSentMessage === false ? null : [
                [
                    'MessageId' => 'message-id',
                    'ReceiptHandle' => 'receipt-handle',
                    'Body' => $lastSentMessage['MessageBody'],
                    'Attributes' => ['ApproximateReceiveCount' => '1'],
                ],
            ],
        ]);
    });

    $sqs->shouldReceive('deleteMessage')->andReturnUsing(function (array $message) use (&$deletedMessages): Result {
        $deletedMessages[] = $message;

        return new Result([]);
    });

    $sqs->shouldReceive('changeMessageVisibility')->andReturnUsing(function (array $message) use (&$releasedMessages): Result {
        $releasedMessages[] = $message;

        return new Result([]);
    });

    assert($sqs instanceof SqsClient);

    $queue = new TenantFairSqsQueue(
        $sqs,
        'default',
        'https://sqs.us-east-1.amazonaws.com/123456789012',
        '',
        false,
        config('queue.connections.sqs.overflow'),
    );
    $queue->setContainer(app());
    $queue->setConnectionName('sqs');

    return $queue;
}

/**
 * Swaps S3 for one in-memory store per tenant (keyed `landlord` without one).
 */
function fakeTenantFairSqsOverflowStorage(): SqsOverflowStorage
{
    $storage = new class () extends SqsOverflowStorage {
        /**
         * @var array<string, Repository>
         */
        private array $stores = [];

        public function store(?string $tenantId): Repository
        {
            return $this->stores[$tenantId ?? 'landlord'] ??= Cache::repository(new ArrayStore());
        }
    };

    app()->instance(SqsOverflowStorage::class, $storage);

    return $storage;
}

function largeQueuedClosure(): CallQueuedClosure
{
    $padding = str_repeat('a', SqsQueue::MAX_SQS_PAYLOAD_SIZE);

    return CallQueuedClosure::create(static fn () => $padding);
}

it('tags jobs pushed from a tenant context with the tenant as the message group', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    $job = CallQueuedClosure::create(static fn () => null);

    $tenant = Tenant::query()->first();

    $tenant->execute(fn () => $queue->push($job));

    expect($sentMessages)->toHaveCount(1)
        ->and($sentMessages[0]['MessageGroupId'])->toBe($tenant->getKey());
});

it('keeps the message group set on the job', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    $job = CallQueuedClosure::create(static fn () => null)->onGroup('explicit-group');

    Tenant::query()->first()->execute(fn () => $queue->push($job));

    expect($sentMessages[0]['MessageGroupId'])->toBe('explicit-group');
});

it('does not set a message group on jobs pushed outside a tenant context', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    Tenant::forgetCurrent();

    $queue->push(CallQueuedClosure::create(static fn () => null));

    expect($sentMessages[0])->not->toHaveKey('MessageGroupId');
});

it('tags every job in a batch with the tenant as the message group', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    $jobs = [
        CallQueuedClosure::create(static fn () => null),
        CallQueuedClosure::create(static fn () => null),
    ];

    $tenant = Tenant::query()->first();

    $tenant->execute(fn () => $queue->bulk($jobs));

    expect($sentMessages)->toHaveCount(2)
        ->and(array_column($sentMessages, 'MessageGroupId'))->toBe([$tenant->getKey(), $tenant->getKey()]);
});

it('moves `pushedAt` to when a delayed job becomes available', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    $job = CallQueuedClosure::create(static fn () => null);

    $pushStartedAt = microtime(true);

    Tenant::query()->first()->execute(fn () => $queue->later(60, $job));

    $payload = json_decode($sentMessages[0]['MessageBody'], true);

    expect($sentMessages[0]['DelaySeconds'])->toBe(60)
        ->and($payload['pushedAt'])->toBeGreaterThanOrEqual($pushStartedAt + 60)
        ->and($payload['pushedAt'])->toBeLessThanOrEqual(microtime(true) + 60);
});

describe('overflow', function () {
    it('keeps payloads under 1 MiB in the message', function () {
        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        $padding = str_repeat('a', SqsQueue::MAX_SQS_PAYLOAD_SIZE - 10000);
        $job = CallQueuedClosure::create(static fn () => $padding);

        Tenant::query()->first()->execute(fn () => $queue->push($job));

        expect(json_decode($sentMessages[0]['MessageBody'], true))->toHaveKey('uuid');
    });

    it('stores a payload of 1 MiB or more under the dispatching tenant and names the tenant in the message', function () {
        $overflowStorage = fakeTenantFairSqsOverflowStorage();

        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        $job = largeQueuedClosure();

        $tenant = Tenant::query()->first();

        $tenant->execute(fn () => $queue->push($job));

        $body = json_decode($sentMessages[0]['MessageBody'], true);

        expect($body['tenantId'])->toBe($tenant->getKey())
            ->and($sentMessages[0]['MessageGroupId'])->toBe($tenant->getKey())
            ->and($overflowStorage->store($tenant->getKey())->get($body['@pointer']))->toContain('"uuid"')
            ->and($overflowStorage->store(null)->get($body['@pointer']))->toBeNull();
    });

    it('stores a payload of 1 MiB or more from a landlord context under the landlord', function () {
        $overflowStorage = fakeTenantFairSqsOverflowStorage();

        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        Tenant::forgetCurrent();

        $queue->push(largeQueuedClosure());

        $body = json_decode($sentMessages[0]['MessageBody'], true);

        expect($body)->not->toHaveKey('tenantId')
            ->and($overflowStorage->store(null)->get($body['@pointer']))->toContain('"uuid"')
            ->and($overflowStorage->store(Tenant::query()->first()->getKey())->get($body['@pointer']))->toBeNull();
    });

    it('stores large payloads sent in a batch under the dispatching tenant', function () {
        $overflowStorage = fakeTenantFairSqsOverflowStorage();

        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        $jobs = [
            largeQueuedClosure(),
            CallQueuedClosure::create(static fn () => null),
        ];

        $tenant = Tenant::query()->first();

        $tenant->execute(fn () => $queue->bulk($jobs));

        $largeBody = json_decode($sentMessages[0]['MessageBody'], true);

        expect($largeBody['tenantId'])->toBe($tenant->getKey())
            ->and($overflowStorage->store($tenant->getKey())->get($largeBody['@pointer']))->toContain('"uuid"')
            ->and(json_decode($sentMessages[1]['MessageBody'], true))->toHaveKey('uuid');
    });

    it('fails the dispatch without sending the message when the payload cannot be stored', function () {
        app()->instance(SqsOverflowStorage::class, new class () extends SqsOverflowStorage {
            public function store(?string $tenantId): Repository
            {
                return Cache::repository(new class () extends ArrayStore {
                    public function put($key, $value, $seconds)
                    {
                        return false;
                    }
                });
            }
        });

        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        $job = largeQueuedClosure();

        expect(fn () => Tenant::query()->first()->execute(fn () => $queue->push($job)))
            ->toThrow(RuntimeException::class, 'could not be offloaded');

        expect($sentMessages)->toBeEmpty();
    });

    it('reads a large tenant payload back from a landlord context and deletes it with the job', function () {
        $overflowStorage = fakeTenantFairSqsOverflowStorage();

        $sentMessages = [];
        $deletedMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages, $deletedMessages);

        $job = largeQueuedClosure();

        $tenant = Tenant::query()->first();

        $tenant->execute(fn () => $queue->push($job));

        $pointer = json_decode($sentMessages[0]['MessageBody'], true)['@pointer'];
        $storedPayload = $overflowStorage->store($tenant->getKey())->get($pointer);

        Tenant::forgetCurrent();

        $poppedJob = $queue->pop();

        assert($poppedJob instanceof TenantFairSqsJob);

        expect($poppedJob->getRawBody())->toBe($storedPayload)
            ->and($poppedJob->payload()['data']['commandName'])->toBe(CallQueuedClosure::class);

        $poppedJob->delete();

        expect($deletedMessages)->toHaveCount(1)
            ->and($overflowStorage->store($tenant->getKey())->get($pointer))->toBeNull();
    });

    it('round-trips a large landlord payload through S3', function () {
        Storage::fake('sqs-overflow');

        $sentMessages = [];
        $queue = tenantFairSqsQueueRecordingInto($sentMessages);

        Tenant::forgetCurrent();

        $queue->push(largeQueuedClosure());

        expect(Storage::disk('sqs-overflow')->allFiles())->toHaveCount(1);

        $poppedJob = $queue->pop();

        assert($poppedJob instanceof TenantFairSqsJob);

        expect($poppedJob->payload()['data']['commandName'])->toBe(CallQueuedClosure::class);

        $poppedJob->delete();

        expect(Storage::disk('sqs-overflow')->allFiles())->toBeEmpty();
    });
});

it('pops messages as tenant fair SQS jobs', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    $queue->push(CallQueuedClosure::create(static fn () => null));

    expect($queue->pop())->toBeInstanceOf(TenantFairSqsJob::class);
});

enum TenantFairSqsQueueTestQueue: string
{
    case Audit = 'audit';
}

it('names a popped job by its queue but deletes and releases it through the queue URL', function (UnitEnum|string|null $poppedQueue, string $expectedName, string $expectedUrl) {
    $sentMessages = [];
    $deletedMessages = [];
    $releasedMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages, $deletedMessages, $releasedMessages);

    $queue->push(CallQueuedClosure::create(static fn () => null));

    $job = $queue->pop($poppedQueue);

    assert($job instanceof TenantFairSqsJob);

    $job->release();
    $job->delete();

    expect($job->getQueue())->toBe($expectedName)
        ->and($releasedMessages[0]['QueueUrl'])->toBe($expectedUrl)
        ->and($deletedMessages[0]['QueueUrl'])->toBe($expectedUrl);
})->with([
    'the default queue' => [null, 'default', 'https://sqs.us-east-1.amazonaws.com/123456789012/default'],
    'an empty queue name' => ['', 'default', 'https://sqs.us-east-1.amazonaws.com/123456789012/default'],
    'a named queue' => ['audit', 'audit', 'https://sqs.us-east-1.amazonaws.com/123456789012/audit'],
    'a queue enum' => [TenantFairSqsQueueTestQueue::Audit, 'audit', 'https://sqs.us-east-1.amazonaws.com/123456789012/audit'],
]);

it('pops nothing when the queue is empty', function () {
    $sentMessages = [];
    $queue = tenantFairSqsQueueRecordingInto($sentMessages);

    expect($queue->pop())->toBeNull();
});
