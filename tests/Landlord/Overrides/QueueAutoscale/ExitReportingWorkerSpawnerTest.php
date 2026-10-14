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

use App\Overrides\QueueAutoscale\ExitReportingWorkerProcess;
use App\Overrides\QueueAutoscale\ExitReportingWorkerSpawner;
use Cbox\LaravelQueueAutoscale\Configuration\SpawnCompensationConfiguration;
use Cbox\LaravelQueueAutoscale\Configuration\WorkerConfiguration;
use Cbox\LaravelQueueAutoscale\Workers\SpawnLatency\NullSpawnLatencyTracker;
use Cbox\LaravelQueueAutoscale\Workers\WorkerProcess;

it('spawns workers that report how they exit', function () {
    config(['queue-autoscale.manager.log_channel' => 'null']);

    // Starts a stand-in process rather than a real queue worker.
    $spawner = new readonly class (new NullSpawnLatencyTracker()) extends ExitReportingWorkerSpawner {
        #[Override]
        public function buildCommand(string $connection, string $queue, WorkerConfiguration $workerConfig): array
        {
            return [PHP_BINARY, '-r', 'sleep(30);'];
        }
    };

    $workers = $spawner->spawn('sqs', 'default', 2, new SpawnCompensationConfiguration(
        enabled: false,
        fallbackSeconds: 2.0,
        minSamples: 5,
        emaAlpha: 0.2,
    ));

    try {
        expect($workers)->toHaveCount(2)
            ->each->toBeInstanceOf(ExitReportingWorkerProcess::class);

        foreach ($workers as $worker) {
            expect([$worker->connection, $worker->queue, $worker->pid()])
                ->toBe(['sqs', 'default', $worker->process->getPid()]);
        }
    } finally {
        $workers->each(fn (WorkerProcess $worker) => $worker->process->stop(0));
    }
});
