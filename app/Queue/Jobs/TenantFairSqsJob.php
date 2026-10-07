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

namespace App\Queue\Jobs;

use App\Queue\SqsOverflowStorage;
use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Queue\Jobs\SqsJob;
use Override;
use RuntimeException;

class TenantFairSqsJob extends SqsJob
{
    /**
     * @param array<string, mixed> $job
     * @param array<string, mixed> $overflowStorage
     */
    public function __construct(
        Container $container,
        SqsClient $sqs,
        array $job,
        string $connectionName,
        string $queueUrl,
        array $overflowStorage,
        private readonly string $queueName,
    ) {
        parent::__construct($container, $sqs, $job, $connectionName, $queueUrl, $overflowStorage);
    }

    /**
     * The queue's name rather than SqsJob's URL, which everything that keys data by queue reads back by name.
     * Revert once upstream is fixed: docs/explanations/oss-todo/cbox-queue-packages-queue-identity.md
     */
    #[Override]
    public function getQueue(): string
    {
        return $this->queueName;
    }

    /**
     * A message whose offloaded payload is gone can never be processed, so it is deleted rather than redelivered
     * until the queue's retention period expires.
     *
     * @throws RuntimeException
     */
    #[Override]
    public function getRawBody(): string
    {
        if ($this->cachedRawBody !== null) {
            return $this->cachedRawBody;
        }

        $legacyPointer = $this->legacyPointer();

        $rawBody = $legacyPointer
            ? $this->overflowStorage()->disk($this->tenantId())?->get($legacyPointer)
            : parent::getRawBody();

        if (! is_string($rawBody)) {
            if (! $this->isDeleted()) {
                $this->delete();
            }

            throw new RuntimeException("The offloaded payload of SQS message [{$this->getJobId()}] could not be found.");
        }

        return $this->cachedRawBody = $rawBody;
    }

    #[Override]
    public function delete(): void
    {
        parent::delete();

        $legacyPointer = $this->legacyPointer();

        if ($legacyPointer) {
            $this->overflowStorage()->disk($this->tenantId())?->delete($legacyPointer);
        }
    }

    #[Override]
    protected function overflowStore(): Repository
    {
        return $this->overflowStorage()->store($this->tenantId());
    }

    protected function tenantId(): ?string
    {
        $body = json_decode($this->job['Body'] ?? '', true);

        return is_string($body['tenantId'] ?? null) ? $body['tenantId'] : null;
    }

    /**
     * TODO: Cleanup Task (sqs-native-overflow): remove the legacy pointer handling from this class, once messages
     * sent by the removed `defectivecode/laravel-sqs-extended` driver have left the queues.
     *
     * That driver sent `{"pointer": "sqs-payloads/<uuid>.json", "tenantId": "<id>"}` in place of large payloads.
     */
    protected function legacyPointer(): ?string
    {
        $body = json_decode($this->job['Body'] ?? '', true);

        if (! is_array($body) || ! is_string($body['pointer'] ?? null) || isset($body['job'])) {
            return null;
        }

        return $body['pointer'];
    }

    protected function overflowStorage(): SqsOverflowStorage
    {
        return $this->container->make(SqsOverflowStorage::class);
    }
}
