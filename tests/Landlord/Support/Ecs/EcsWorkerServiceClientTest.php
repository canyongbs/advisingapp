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

use App\Support\Ecs\EcsTaskIdentity;
use App\Support\Ecs\EcsWorkerServiceClient;
use Aws\CommandInterface;
use Aws\Ecs\EcsClient;
use Aws\MockHandler;
use Aws\Result;
use Illuminate\Support\Facades\Http;

$clientFor = fn (MockHandler $aws): EcsWorkerServiceClient => new EcsWorkerServiceClient(new EcsClient([
    'region' => 'us-west-2',
    'version' => 'latest',
    'credentials' => false,
    'handler' => $aws,
]));

/**
 * Queues the AWS response for the next call, which must be $operation, and records the call's parameters.
 *
 * @param ArrayObject<int, array<string, mixed>> $calls
 * @param array<string, mixed> $result
 */
$respond = fn (MockHandler $aws, ArrayObject $calls, string $operation, array $result) => $aws->append(
    function (CommandInterface $command) use ($calls, $operation, $result): Result {
        expect($command->getName())->toBe($operation);

        $calls[] = $command->toArray();

        return new Result($result);
    },
);

it('does nothing off ECS', function () use ($clientFor) {
    Http::preventStrayRequests();
    config(['app.ecs_container_metadata_uri' => '']);

    $aws = new MockHandler();

    expect($clientFor($aws)->identifyCurrentTask())->toBeNull()
        ->and($aws->count())->toBe(0);
});

it('identifies the task and the service deployment that started it', function () use ($clientFor, $respond) {
    config(['app.ecs_container_metadata_uri' => 'http://169.254.170.2/v4/abc']);
    Http::preventStrayRequests();
    Http::fake(['http://169.254.170.2/v4/abc/task' => Http::response([
        'TaskARN' => 'arn:aws:ecs:us-west-2:111111111111:task/advisingapp-dev/task-a',
        'Cluster' => 'arn:aws:ecs:us-west-2:111111111111:cluster/advisingapp-dev',
    ])]);

    $aws = new MockHandler();
    $calls = new ArrayObject();

    $respond($aws, $calls, 'DescribeTasks', ['tasks' => [[
        'group' => 'service:advisingapp-worker-dev-service',
        'startedBy' => 'ecs-svc/111',
    ]]]);

    expect($clientFor($aws)->identifyCurrentTask())->toEqual(new EcsTaskIdentity(
        taskArn: 'arn:aws:ecs:us-west-2:111111111111:task/advisingapp-dev/task-a',
        cluster: 'arn:aws:ecs:us-west-2:111111111111:cluster/advisingapp-dev',
        serviceName: 'advisingapp-worker-dev-service',
        startedBy: 'ecs-svc/111',
    ))
        ->and($calls[0])->toMatchArray([
            'cluster' => 'arn:aws:ecs:us-west-2:111111111111:cluster/advisingapp-dev',
            'tasks' => ['arn:aws:ecs:us-west-2:111111111111:task/advisingapp-dev/task-a'],
        ]);
});

it('does not identify a task that no service started', function () use ($clientFor, $respond) {
    config(['app.ecs_container_metadata_uri' => 'http://169.254.170.2/v4/abc']);
    Http::fake(['*' => Http::response([
        'TaskARN' => 'arn:aws:ecs:us-west-2:111111111111:task/advisingapp-dev/task-a',
        'Cluster' => 'arn:aws:ecs:us-west-2:111111111111:cluster/advisingapp-dev',
    ])]);

    $aws = new MockHandler();

    $respond($aws, new ArrayObject(), 'DescribeTasks', ['tasks' => [[
        'group' => 'family:advisingapp-worker-dev',
        'startedBy' => 'someone',
    ]]]);

    expect($clientFor($aws)->identifyCurrentTask())->toBeNull();
});

it('describes the service deployments and its desired and running counts', function () use ($clientFor, $respond) {
    $aws = new MockHandler();
    $calls = new ArrayObject();

    $respond($aws, $calls, 'DescribeServices', ['services' => [[
        'desiredCount' => 2,
        'runningCount' => 3,
        'deployments' => [
            ['id' => 'ecs-svc/222', 'status' => 'PRIMARY'],
            ['id' => 'ecs-svc/111', 'status' => 'ACTIVE'],
        ],
    ]]]);

    $state = $clientFor($aws)->describeService(new EcsTaskIdentity('task-a', 'cluster-arn', 'advisingapp-worker-dev-service', 'ecs-svc/111'));

    expect($state->primaryDeploymentId)->toBe('ecs-svc/222')
        ->and($state->deploymentCount)->toBe(2)
        ->and($state->desiredCount)->toBe(2)
        ->and($state->runningCount)->toBe(3)
        ->and($calls[0])->toMatchArray([
            'cluster' => 'cluster-arn',
            'services' => ['advisingapp-worker-dev-service'],
        ]);
});

it('fails rather than describe a service without a primary deployment', function () use ($clientFor, $respond) {
    $aws = new MockHandler();

    $respond($aws, new ArrayObject(), 'DescribeServices', ['services' => [[
        'desiredCount' => 2,
        'runningCount' => 2,
        'deployments' => [
            ['id' => 'ecs-svc/111', 'status' => 'ACTIVE'],
        ],
    ]]]);

    $clientFor($aws)->describeService(new EcsTaskIdentity('task-a', 'cluster-arn', 'advisingapp-worker-dev-service', 'ecs-svc/111'));
})->throws(UnexpectedValueException::class);

it('lists every task the service is keeping running', function () use ($clientFor, $respond) {
    $aws = new MockHandler();
    $calls = new ArrayObject();

    $respond($aws, $calls, 'ListTasks', ['taskArns' => ['task-a', 'task-b'], 'nextToken' => 'page-2']);
    $respond($aws, $calls, 'ListTasks', ['taskArns' => ['task-c']]);

    $runningTaskArns = $clientFor($aws)->runningTaskArns(new EcsTaskIdentity('task-a', 'cluster-arn', 'advisingapp-worker-dev-service', 'ecs-svc/111'));

    expect($runningTaskArns)->toBe(['task-a', 'task-b', 'task-c'])
        ->and($calls[0])->toMatchArray([
            'cluster' => 'cluster-arn',
            'serviceName' => 'advisingapp-worker-dev-service',
            'desiredStatus' => 'RUNNING',
        ]);
});

it('finds the tasks ECS can stop without a drain: unhealthy, unprotected, expired and unreported', function () use ($clientFor, $respond) {
    $aws = new MockHandler();
    $calls = new ArrayObject();
    $taskArns = ['task-b', 'task-c', 'task-d', 'task-e', 'task-f', 'task-g', 'task-h', 'task-i', 'task-j', 'task-k', 'task-l'];
    $protected = fn (string $taskArn): array => ['taskArn' => $taskArn, 'protectionEnabled' => true, 'expirationDate' => new DateTimeImmutable('+10 minutes')];

    $respond($aws, $calls, 'DescribeTasks', ['tasks' => [
        ['taskArn' => 'task-b', 'healthStatus' => 'HEALTHY'],
        ['taskArn' => 'task-c', 'healthStatus' => 'UNHEALTHY'],
    ]]);
    $respond($aws, $calls, 'GetTaskProtection', ['protectedTasks' => [
        $protected('task-b'),
        $protected('task-c'),
        ['taskArn' => 'task-d', 'protectionEnabled' => false],
        ['taskArn' => 'task-e', 'protectionEnabled' => true, 'expirationDate' => new DateTimeImmutable('-1 minute')],
        ...array_map($protected, ['task-f', 'task-g', 'task-h', 'task-i', 'task-j']),
    ]]);
    $respond($aws, $calls, 'GetTaskProtection', ['protectedTasks' => [$protected('task-l')]]);

    $stoppableTaskArns = $clientFor($aws)->stoppableTaskArns(new EcsTaskIdentity('task-a', 'cluster-arn', 'advisingapp-worker-dev-service', 'ecs-svc/111'), $taskArns);

    sort($stoppableTaskArns);

    $protectionBatches = array_map(fn (array $call): mixed => $call['tasks'], array_slice($calls->getArrayCopy(), 1));

    expect($stoppableTaskArns)->toBe(['task-c', 'task-d', 'task-e', 'task-k'])
        ->and($protectionBatches)->toBe([array_slice($taskArns, 0, 10), ['task-l']]);
});
