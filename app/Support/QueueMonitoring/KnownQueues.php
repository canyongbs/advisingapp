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

namespace App\Support\QueueMonitoring;

use App\Support\QueueAutoscale\WorkerCountHistory;
use Illuminate\Support\Facades\Config;

/**
 * The queues shown on the queue monitoring page: the configured queues, plus any other queue the autoscaler has
 * sampled recently. Configured queues are labeled by their config key, so they read the same in every environment.
 */
class KnownQueues
{
    public function __construct(
        private WorkerCountHistory $workerCountHistory,
    ) {}

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return array_values(array_unique([
            ...array_values($this->configured()),
            ...$this->workerCountHistory->queues(),
        ]));
    }

    public function label(string $queue): string
    {
        $configuredLabel = array_search($queue, $this->configured(), true);

        if (is_string($configuredLabel)) {
            return $configuredLabel;
        }

        return $queue;
    }

    /**
     * @return array<string, string>
     */
    private function configured(): array
    {
        $configured = [];

        foreach (Config::array('queue.queues', []) as $label => $queue) {
            if (is_string($label) && is_string($queue)) {
                $configured[$label] = $queue;
            }
        }

        return $configured;
    }
}
