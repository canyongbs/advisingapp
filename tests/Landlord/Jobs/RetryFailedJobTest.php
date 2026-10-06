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

use App\Jobs\LoadTestJob;
use App\Jobs\PruneStaleCacheTags;
use App\Jobs\RetryFailedJob;
use App\Models\Tenant;
use App\Queue\TenantFairSqsQueue;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Support\Facades\Queue as QueueFacade;
use Illuminate\Support\Str;

/**
 * Points a `recording-sqs` queue connection at a tenant fair SQS queue whose client records every message sent.
 *
 * @param  array<int, array<string, mixed>>  $sentMessages
 */
function recordFailedJobRetriesInto(array &$sentMessages): void
{
    $sqs = Mockery::mock(SqsClient::class);
    $sqs->shouldReceive('sendMessage')->andReturnUsing(function (array $message) use (&$sentMessages): Result {
        $sentMessages[] = $message;

        return new Result(['MessageId' => 'message-id']);
    });

    assert($sqs instanceof SqsClient);

    QueueFacade::extend('recording-sqs', fn (): ConnectorInterface => new class ($sqs) implements ConnectorInterface {
        public function __construct(private SqsClient $sqs) {}

        /**
         * @param  array<string, mixed>  $config
         */
        public function connect(array $config): Queue
        {
            return new TenantFairSqsQueue($this->sqs, 'default', 'https://sqs.us-east-1.amazonaws.com/123456789012', '', false, config('queue.connections.sqs.overflow'));
        }
    });

    config(['queue.connections.recording-sqs' => ['driver' => 'recording-sqs']]);
}

/**
 * Pushes the job the way a dispatch would, so the payload carries the current tenant, and logs it as failed.
 *
 * @param  array<int, array<string, mixed>>  $sentMessages
 */
function failJob(object $job, string $databaseConnection, array &$sentMessages): string
{
    QueueFacade::connection('recording-sqs')->push($job);

    $payload = array_pop($sentMessages)['MessageBody'];

    assert(is_string($payload));

    RetryFailedJob::failedJobProvider($databaseConnection)->log('recording-sqs', 'default', $payload, new RuntimeException('Failed.'));

    $failedJobId = json_decode($payload, true)['uuid'];

    assert(is_string($failedJobId));

    return $failedJobId;
}

it('retries a landlord failed job and removes it from the landlord failed jobs', function () {
    $sentMessages = [];
    recordFailedJobRetriesInto($sentMessages);

    $failedJobId = failJob(new LoadTestJob(0), 'landlord', $sentMessages);

    (new RetryFailedJob(null, [$failedJobId]))->handle();

    expect($sentMessages)->toHaveCount(1)
        ->and($sentMessages[0]['MessageGroupId'] ?? null)->toBeNull()
        ->and(json_decode($sentMessages[0]['MessageBody'], true)['uuid'])->toBe($failedJobId)
        ->and(RetryFailedJob::failedJobProvider('landlord')->find($failedJobId))->toBeNull();
});

it("retries a tenant's failed job in the tenant's message group and removes it from the tenant's failed jobs", function () {
    $sentMessages = [];
    recordFailedJobRetriesInto($sentMessages);

    $tenant = Tenant::query()->first();
    $tenantConnection = config('multitenancy.tenant_database_connection_name');

    $failedJobId = $tenant->execute(function () use ($tenantConnection, &$sentMessages): string {
        return failJob(new PruneStaleCacheTags(), $tenantConnection, $sentMessages);
    });

    (new RetryFailedJob($tenant->getKey(), [$failedJobId]))->handle();

    expect($sentMessages)->toHaveCount(1)
        ->and($sentMessages[0]['MessageGroupId'])->toBe($tenant->getKey())
        ->and($tenant->execute(fn (): ?object => RetryFailedJob::failedJobProvider($tenantConnection)->find($failedJobId)))->toBeNull()
        ->and(Tenant::current())->toBeNull();
});

it('skips failed jobs that no longer exist', function () {
    $sentMessages = [];
    recordFailedJobRetriesInto($sentMessages);

    (new RetryFailedJob(null, [(string) Str::uuid()]))->handle();

    expect($sentMessages)->toBeEmpty();
});
