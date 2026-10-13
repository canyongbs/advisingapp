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

namespace App\Console\Commands;

use App\Support\Ecs\WorkerDrainCoordinator;
use Illuminate\Console\Command;
use Illuminate\Support\Sleep;

/**
 * Runs for the life of a worker container as its own s6 service, draining the task when ECS wants it gone.
 */
class WatchWorkerDrainCommand extends Command
{
    private const int CHECK_INTERVAL_MILLISECONDS = 10000;

    // Tasks started together would otherwise call the rate-limited ECS API in the same instant.
    private const int CHECK_JITTER_MILLISECONDS = 2000;

    // Checked faster while draining so protection is released soon after the last job finishes.
    private const int DRAINING_CHECK_INTERVAL_MILLISECONDS = 5000;

    protected $signature = 'ecs:worker-drain-watch {--once : Check once and exit}';

    protected $description = 'Drain this worker task when its deployment is replaced or scale-in needs it gone';

    public function handle(WorkerDrainCoordinator $coordinator): int
    {
        $coordinator->check();

        while (! $this->option('once')) {
            Sleep::for($coordinator->isDraining()
                ? self::DRAINING_CHECK_INTERVAL_MILLISECONDS
                : random_int(self::CHECK_INTERVAL_MILLISECONDS - self::CHECK_JITTER_MILLISECONDS, self::CHECK_INTERVAL_MILLISECONDS + self::CHECK_JITTER_MILLISECONDS))->milliseconds();

            $coordinator->check();
        }

        return self::SUCCESS;
    }
}
