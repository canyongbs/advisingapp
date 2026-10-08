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
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Process\Process;

/**
 * @param list<string> $command
 */
function exitReportingWorkerRunning(array $command): ExitReportingWorkerProcess
{
    $process = new Process($command);
    $process->start();

    return new ExitReportingWorkerProcess(
        process: $process,
        connection: 'sqs',
        queue: 'default',
        spawnedAt: now(),
    );
}

/**
 * @return list<MessageLogged>
 */
function workerExitLogs(): array
{
    return Event::dispatched(MessageLogged::class)
        ->map(fn (array $arguments): MessageLogged => $arguments[0])
        ->filter(fn (MessageLogged $message): bool => $message->message === 'Worker exited unexpectedly')
        ->values()
        ->all();
}

beforeEach(function () {
    config(['queue-autoscale.manager.log_channel' => 'null']);

    Event::fake([MessageLogged::class]);
});

it('logs the exit code of a worker that exited on its own', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'exit(3);']);
    $worker->process->wait();

    expect($worker->isDead())->toBeTrue()
        ->and(workerExitLogs())->toHaveCount(1)
        ->and(workerExitLogs()[0]->level)->toBe('warning')
        ->and(workerExitLogs()[0]->context)->toMatchArray([
            'pid' => $worker->pid(),
            'connection' => 'sqs',
            'queue' => 'default',
            'exit_code' => 3,
            'term_signal' => null,
            'termination_requested' => false,
        ]);
});

it('logs the signal that killed a worker', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'sleep(30);']);
    $worker->process->signal(SIGKILL);
    $worker->process->wait();

    expect($worker->isDead())->toBeTrue()
        ->and(workerExitLogs())->toHaveCount(1)
        ->and(workerExitLogs()[0]->context['term_signal'])->toBe(SIGKILL);
});

it('logs a worker that exited with an error after its termination was requested', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'exit(12);']);
    $worker->markTerminationRequested(now(), 60);
    $worker->process->wait();

    expect($worker->isDead())->toBeTrue()
        ->and(workerExitLogs())->toHaveCount(1)
        ->and(workerExitLogs()[0]->context)->toMatchArray([
            'exit_code' => 12,
            'termination_requested' => true,
        ]);
});

it('does not log a worker that exited cleanly after its termination was requested', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'exit(0);']);
    $worker->markTerminationRequested(now(), 60);
    $worker->process->wait();

    expect($worker->isDead())->toBeTrue()
        ->and(workerExitLogs())->toBeEmpty();
});

it('logs a dead worker only once', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'exit(3);']);
    $worker->process->wait();

    $worker->isDead();
    $worker->isDead();

    expect(workerExitLogs())->toHaveCount(1);
});

it('does not log a running worker', function () {
    $worker = exitReportingWorkerRunning([PHP_BINARY, '-r', 'sleep(30);']);

    try {
        expect($worker->isDead())->toBeFalse()
            ->and(workerExitLogs())->toBeEmpty();
    } finally {
        $worker->process->stop(0);
    }
});
