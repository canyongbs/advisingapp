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

namespace App\Support\QueueAutoscale;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * Controls the worker container's `queue-autoscale` s6 service. Stopping it makes the manager SIGTERM its workers,
 * which finish their current job and take no more, and s6 does not restart it until it is started again.
 */
class QueueAutoscaleService
{
    public const string SERVICE_DIRECTORY = '/run/service/queue-autoscale';

    public function __construct(
        private string $procDirectory = '/proc',
    ) {}

    public function stop(): void
    {
        Process::run(['/command/s6-svc', '-d', self::SERVICE_DIRECTORY])->throw();
    }

    public function start(): void
    {
        Process::run(['/command/s6-svc', '-u', self::SERVICE_DIRECTORY])->throw();
    }

    public function isRunning(): bool
    {
        $result = Process::run(['/command/s6-svstat', '-u', self::SERVICE_DIRECTORY]);

        return $result->successful() && (Str::trim($result->output()) === 'true');
    }

    /**
     * Includes workers that outlived their manager, which nothing else will stop.
     *
     * @return list<int>
     */
    public function workerProcessIds(): array
    {
        $processIds = [];

        foreach (glob("{$this->procDirectory}/[0-9]*/cmdline") ?: [] as $path) {
            $commandLine = @file_get_contents($path);

            if (! is_string($commandLine)) {
                continue;
            }

            if (in_array('queue:work', explode("\0", $commandLine), strict: true)) {
                $processIds[] = (int) basename(dirname($path));
            }
        }

        return $processIds;
    }

    public function terminateWorkers(): void
    {
        foreach ($this->workerProcessIds() as $processId) {
            posix_kill($processId, SIGTERM);
        }
    }
}
