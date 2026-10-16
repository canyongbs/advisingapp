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

use App\Providers\EcsTaskProtectionServiceProvider;
use App\Queue\Jobs\TenantFairSqsJob;
use App\Support\Ecs\EcsTaskProtector;
use Aws\CommandInterface;
use Aws\Ecs\EcsClient;
use Aws\Result;
use Aws\Sqs\SqsClient;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // The other JobProcessing listeners need a full job payload, so only this provider's listener is kept.
    Event::forget(JobProcessing::class);

    $provider = app()->getProvider(EcsTaskProtectionServiceProvider::class);

    assert($provider instanceof EcsTaskProtectionServiceProvider);

    $provider->boot();
});

// Teardown refreshes the tenant database, whose queued listeners now also reach the protector.
afterEach(function () {
    app()->forgetInstance(EcsTaskProtector::class);
});

function jobProcessingWithTimeout(?int $timeout): JobProcessing
{
    return new JobProcessing('sync', new SyncJob(app(), json_encode(['timeout' => $timeout]), 'sync', 'default'));
}

it('protects the ECS task while a job with a long timeout runs', function (int $timeout) {
    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldReceive('protectForJobTimeout')->once()->with($timeout);
    app()->instance(EcsTaskProtector::class, $protector);

    event(jobProcessingWithTimeout($timeout));
})->with([
    'at the threshold' => 100,
    'above the threshold' => 1140,
]);

it('protects the ECS task for a job without its own timeout using the worker timeout for its queue', function () {
    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldReceive('protectForJobTimeout')->once()->with(600);
    app()->instance(EcsTaskProtector::class, $protector);

    event(jobProcessingWithTimeout(null));
});

it('does not protect the ECS task for a short job timeout', function () {
    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldNotReceive('protectForJobTimeout');
    app()->instance(EcsTaskProtector::class, $protector);

    event(jobProcessingWithTimeout(99));
});

it('does not protect the ECS task for a job without its own timeout when the worker timeout is short', function () {
    config(['queue-autoscale.queues.short-timeout-queue' => ['workers' => ['timeout_seconds' => 60]]]);

    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldNotReceive('protectForJobTimeout');
    app()->instance(EcsTaskProtector::class, $protector);

    $job = Mockery::mock(Job::class);
    $job->allows('payload')->andReturn(['timeout' => null]);
    $job->allows('getQueue')->andReturn('short-timeout-queue');

    assert($job instanceof Job);

    event(new JobProcessing('sqs', $job));
});

it('resolves the worker timeout of an SQS job by its queue name rather than its queue URL', function () {
    config([
        'queue.connections.sqs.suffix' => '-production',
        'queue-autoscale.queues.long-timeout-queue' => ['workers' => ['timeout_seconds' => 1140]],
    ]);

    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldReceive('protectForJobTimeout')->once()->with(1140);
    app()->instance(EcsTaskProtector::class, $protector);

    $sqs = Mockery::mock(SqsClient::class);

    assert($sqs instanceof SqsClient);

    $job = new TenantFairSqsJob(
        app(),
        $sqs,
        [
            'MessageId' => 'message-id',
            'ReceiptHandle' => 'receipt-handle',
            'Body' => json_encode(['timeout' => null]),
            'Attributes' => ['ApproximateReceiveCount' => '1'],
        ],
        'sqs',
        'https://sqs.us-east-1.amazonaws.com/123456789012/long-timeout-queue-production',
        config('queue.connections.sqs.overflow'),
    );

    event(new JobProcessing('sqs', $job));
});

it('gives up on ECS API calls that hang instead of waiting forever', function () {
    $client = app(EcsClient::class);
    $httpOptions = new ArrayObject();

    $client->getHandlerList()->remove('signer');
    $client->getHandlerList()->setHandler(function (CommandInterface $command) use ($httpOptions): PromiseInterface {
        $httpOptions->exchangeArray($command['@http']);

        return Create::promiseFor(new Result([]));
    });

    $client->listTasks(['cluster' => 'cluster-arn']);

    expect($httpOptions->getArrayCopy())->toMatchArray([
        'connect_timeout' => 5,
        'timeout' => 10,
    ]);
});
