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

use App\Jobs\LoadTestJob;
use Illuminate\Console\Command;

class QueueLoadTestCommand extends Command
{
    protected $signature = 'queue:loadtest
        {queue : The queue name to dispatch onto}
        {count : Number of synthetic jobs to dispatch}
        {--duration=2 : Seconds each job sleeps}
        {--group= : The SQS message group to send the jobs in, to simulate a noisy or quiet tenant}';

    protected $description = 'Dispatch synthetic sleep-only jobs to exercise queue autoscaling. Refuses to run in production.';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('queue:loadtest is disabled in production.');

            return self::FAILURE;
        }

        $queue = $this->argument('queue');
        $count = (int) $this->argument('count');
        $duration = (int) $this->option('duration');
        $group = $this->option('group');

        if ($count < 1 || $duration < 0) {
            $this->error('count must be positive and duration must not be negative.');

            return self::FAILURE;
        }

        $this->info("Dispatching {$count} job(s) sleeping {$duration}s each onto [{$queue}]...");

        $this->withProgressBar(range(1, $count), function () use ($duration, $queue, $group): void {
            $job = (new LoadTestJob($duration))->onQueue($queue);

            if (filled($group)) {
                $job->onGroup($group);
            }

            dispatch($job);
        });

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
