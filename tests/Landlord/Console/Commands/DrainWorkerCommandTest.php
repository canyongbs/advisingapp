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

use App\Support\Ecs\WorkerDrainCoordinator;

use function Pest\Laravel\artisan;

it('drains the worker', function () {
    $coordinator = Mockery::mock(WorkerDrainCoordinator::class);
    $coordinator->expects('drainManually')->once();
    $coordinator->expects('resume')->never();
    app()->instance(WorkerDrainCoordinator::class, $coordinator);

    artisan('ecs:worker-drain')->assertSuccessful();
});

it('cancels the drain', function () {
    $coordinator = Mockery::mock(WorkerDrainCoordinator::class);
    $coordinator->allows('isDraining')->andReturnTrue();
    $coordinator->expects('resume')->once()->andReturnTrue();
    $coordinator->expects('drainManually')->never();
    app()->instance(WorkerDrainCoordinator::class, $coordinator);

    artisan('ecs:worker-drain', ['--cancel' => true])
        ->expectsOutput('Draining cancelled; this worker takes jobs again.')
        ->assertSuccessful();
});

it('reports a drain that cannot be cancelled', function () {
    $coordinator = Mockery::mock(WorkerDrainCoordinator::class);
    $coordinator->allows('isDraining')->andReturnTrue();
    $coordinator->expects('resume')->once()->andReturnFalse();
    app()->instance(WorkerDrainCoordinator::class, $coordinator);

    artisan('ecs:worker-drain', ['--cancel' => true])
        ->expectsOutput('This drain cannot be cancelled: a deployment is replacing the task, or it has already released its ECS protection.')
        ->assertFailed();
});

it('has nothing to cancel when the worker is not draining', function () {
    $coordinator = Mockery::mock(WorkerDrainCoordinator::class);
    $coordinator->allows('isDraining')->andReturnFalse();
    $coordinator->expects('resume')->never();
    app()->instance(WorkerDrainCoordinator::class, $coordinator);

    artisan('ecs:worker-drain', ['--cancel' => true])
        ->expectsOutput('This worker is not draining.')
        ->assertSuccessful();
});
