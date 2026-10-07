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
use App\Queue\Jobs\TenantFairSqsJob;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Str;
use Override;
use RuntimeException;

/**
 * Tags every job pushed from a tenant context with an SQS `MessageGroupId` of the tenant's id, so SQS fair queues
 * keep one tenant's backlog from delaying the others on the shared queues, and keeps payloads too large for SQS under
 * that tenant's S3 root.
 */
class TenantFairSqsQueue extends SqsQueue
{
    /**
     * @param mixed $job
     * @param string|null $queue
     * @param string $payload
     *
     * @return array{DelaySeconds?: int, MessageGroupId?: string, MessageDeduplicationId?: string}
     */
    #[Override]
    public function getQueueableOptions($job, $queue, $payload, $delay = null): array
    {
        $options = parent::getQueueableOptions($job, $queue, $payload, $delay);

        if (isset($options['MessageGroupId'])) {
            return $options;
        }

        $tenantId = Tenant::current()?->getKey();

        if ($tenantId === null) {
            return $options;
        }

        $options['MessageGroupId'] = (string) $tenantId;

        return $options;
    }

    /**
     * @param string|null $queue
     */
    #[Override]
    public function pop($queue = null): ?TenantFairSqsJob
    {
        $response = $this->sqs->receiveMessage([
            'QueueUrl' => $queue = $this->getQueue($queue),
            'AttributeNames' => ['ApproximateReceiveCount'],
        ]);

        if (empty($response['Messages'])) {
            return null;
        }

        return new TenantFairSqsJob(
            $this->container,
            $this->sqs,
            $response['Messages'][0],
            $this->connectionName,
            $queue,
            $this->overflowStorage,
        );
    }

    /**
     * The tenant id travels in the message body beside the pointer, so a worker knows where the payload is before it
     * reads it.
     *
     * @param string $payload
     */
    #[Override]
    protected function overflow($payload): string
    {
        $decodedPayload = json_decode($payload);

        $pointer = static::EXTENDED_PAYLOAD_CACHE_PREFIX . (is_object($decodedPayload) && isset($decodedPayload->uuid)
            ? $decodedPayload->uuid
            : Str::uuid());

        $tenantId = Tenant::current()?->getKey();

        assert($tenantId === null || is_string($tenantId));

        if (! $this->container->make(SqsOverflowStorage::class)->store($tenantId)->put($pointer, $payload)) {
            throw new RuntimeException("The payload of SQS message [{$pointer}] could not be offloaded.");
        }

        return json_encode([
            '@pointer' => $pointer,
            ...($tenantId === null ? [] : ['tenantId' => $tenantId]),
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * Moves `pushedAt` to when a delayed job becomes available, so the delay is not counted as time spent waiting
     * for a worker.
     *
     * @param mixed $job
     * @param string $queue
     * @param mixed $data
     */
    #[Override]
    protected function createPayload($job, $queue, $data = '', $delay = null): string
    {
        $payload = parent::createPayload($job, $queue, $data, $delay);

        if (empty($delay)) {
            return $payload;
        }

        $decodedPayload = json_decode($payload, true);

        if (! is_numeric($decodedPayload['pushedAt'] ?? null)) {
            return $payload;
        }

        $decodedPayload['pushedAt'] = (float) $decodedPayload['pushedAt'] + $this->secondsUntil($delay);

        return json_encode($decodedPayload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
