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

namespace App\Listeners;

use App\Support\QueueAutoscale\WorkerCountHistory;
use Cbox\LaravelQueueAutoscale\Events\ClusterSummaryPublished;
use Cbox\LaravelQueueAutoscale\Events\ScalingDecisionMade;
use Illuminate\Support\Facades\Cache;

/**
 * Samples the autoscaler's provisioned worker count into the worker count history. QueueObservabilityServiceProvider
 * wires exactly one source: in cluster mode each host's ScalingDecisionMade only carries its local share, so the
 * leader's ClusterSummaryPublished is used for the cluster-wide count instead.
 */
class RecordWorkerCountSample
{
    private const int THROTTLE_SECONDS = 60;

    public function __construct(
        private readonly WorkerCountHistory $history,
    ) {}

    public function recordScalingDecision(ScalingDecisionMade $event): void
    {
        $this->sample($event->decision->queue, $event->decision->currentWorkers);
    }

    public function recordClusterSummary(ClusterSummaryPublished $event): void
    {
        $workloads = $event->summary['workloads'] ?? null;

        if (! is_array($workloads)) {
            return;
        }

        foreach ($workloads as $workload) {
            if (! is_array($workload)) {
                continue;
            }

            $queue = $workload['name'] ?? null;
            $count = $workload['current_workers'] ?? null;

            if (! is_string($queue) || ! is_numeric($count)) {
                continue;
            }

            $this->sample($queue, (int) $count);
        }
    }

    private function sample(string $queue, int $count): void
    {
        // The events fire every ~5s; an atomic add keeps it to one sample per queue per minute across every host.
        if (! Cache::store('landlord')->add("queue-worker-count-sample:{$queue}", true, self::THROTTLE_SECONDS)) {
            return;
        }

        $this->history->record($queue, $count);
    }
}
