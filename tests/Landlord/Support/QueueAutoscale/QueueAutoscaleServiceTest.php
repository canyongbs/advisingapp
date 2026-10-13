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

use App\Support\QueueAutoscale\QueueAutoscaleService;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

it('stops the queue autoscaler so s6 does not restart it', function () {
    Process::fake();

    (new QueueAutoscaleService())->stop();

    Process::assertRan(fn (PendingProcess $process): bool => $process->command === ['/command/s6-svc', '-d', '/run/service/queue-autoscale']);
});

it('starts the queue autoscaler again', function () {
    Process::fake();

    (new QueueAutoscaleService())->start();

    Process::assertRan(fn (PendingProcess $process): bool => $process->command === ['/command/s6-svc', '-u', '/run/service/queue-autoscale']);
});

it('reports whether the queue autoscaler is running', function (string $output, int $exitCode, bool $expected) {
    Process::fake([
        '*' => Process::result(output: $output, exitCode: $exitCode),
    ]);

    expect((new QueueAutoscaleService())->isRunning())->toBe($expected);

    Process::assertRan(fn (PendingProcess $process): bool => $process->command === ['/command/s6-svstat', '-u', '/run/service/queue-autoscale']);
})->with([
    'up' => ["true\n", 0, true],
    'down' => ["false\n", 0, false],
    'unsupervised' => ['', 1, false],
]);

it('finds the queue:work processes, and only those', function () {
    $proc = sys_get_temp_dir() . '/queue-autoscale-service-proc-' . Str::random();

    $processes = [
        101 => ['/usr/bin/php8.4', '/var/www/html/artisan', 'queue:work', 'sqs', '--queue=default'],
        102 => ['php', '-d', 'memory_limit=768M', '/var/www/html/artisan', 'queue:autoscale'],
        103 => ['php', '/var/www/html/artisan', 'ecs:worker-drain-watch'],
        104 => ['/usr/bin/php8.4', '/var/www/html/artisan', 'queue:work', 'sqs', '--queue=landlord'],
    ];

    foreach ($processes as $processId => $arguments) {
        File::ensureDirectoryExists("{$proc}/{$processId}");
        File::put("{$proc}/{$processId}/cmdline", implode("\0", $arguments) . "\0");
    }

    File::ensureDirectoryExists("{$proc}/self");

    try {
        $processIds = (new QueueAutoscaleService($proc))->workerProcessIds();

        sort($processIds);

        expect($processIds)->toBe([101, 104]);
    } finally {
        File::deleteDirectory($proc);
    }
});
