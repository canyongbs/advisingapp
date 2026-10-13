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
use App\Support\Ecs\ScaleInDrainClaims;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

use function Pest\Laravel\travel;

/**
 * Registers three running tasks of a fresh service and returns a function that reconciles one of them. Each task's
 * protection runs out this many minutes from now, so task-b is the least busy and task-a the busiest.
 *
 * @return Closure(string, int, bool=, ?list<string>=): bool
 */
$workerService = function (): Closure {
    $protectionMinutes = ['task-a' => 20, 'task-b' => 5, 'task-c' => 12];
    $claims = new ScaleInDrainClaims();
    $cluster = 'cluster-' . Str::random();

    $reconcile = fn (string $taskArn, int $excess, bool $isClaimed = false, ?array $running = ['task-a', 'task-b', 'task-c']): bool => $claims->reconcile(
        new EcsTaskIdentity($taskArn, $cluster, 'worker-service', 'ecs-svc/1'),
        CarbonImmutable::now()->addMinutes($protectionMinutes[$taskArn]),
        $running,
        $excess,
        $isClaimed,
    );

    foreach (array_keys($protectionMinutes) as $taskArn) {
        $reconcile($taskArn, 0);
    }

    return $reconcile;
};

it('drains only the least busy tasks, as many as scale-in needs', function () use ($workerService) {
    $reconcile = $workerService();

    expect($reconcile('task-a', 1))->toBeFalse()
        ->and($reconcile('task-c', 1))->toBeFalse()
        ->and($reconcile('task-b', 1))->toBeTrue();
});

it('does not drain more tasks than scale-in needs', function () use ($workerService) {
    $reconcile = $workerService();

    expect($reconcile('task-b', 1))->toBeTrue()
        ->and($reconcile('task-c', 1))->toBeFalse()
        ->and($reconcile('task-a', 1))->toBeFalse();
});

it('drains the next least busy task when scale-in needs another', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 1);

    expect($reconcile('task-a', 2))->toBeFalse()
        ->and($reconcile('task-c', 2))->toBeTrue();
});

it('keeps a drain while scale-in still needs it', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 1);

    expect($reconcile('task-b', 1, true))->toBeTrue();
});

it('gives back the drains scale-in no longer needs, the busiest first', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 2);
    $reconcile('task-c', 2);

    expect($reconcile('task-c', 1, true))->toBeFalse()
        ->and($reconcile('task-b', 1, true))->toBeTrue()
        ->and($reconcile('task-b', 0, true))->toBeFalse();
});

it('does not count tasks ECS is no longer keeping running', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 1);

    expect($reconcile('task-c', 1, false, ['task-a', 'task-c']))->toBeTrue();
});

it('keeps every task that is checking in when the tasks were not listed', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 1);

    expect($reconcile('task-c', 0, false, null))->toBeFalse()
        ->and($reconcile('task-c', 1))->toBeFalse();
});

it('does not count tasks that stopped checking in', function () use ($workerService) {
    $reconcile = $workerService();
    $reconcile('task-b', 1);

    travel(61)->seconds();

    expect($reconcile('task-c', 1))->toBeTrue();
});
