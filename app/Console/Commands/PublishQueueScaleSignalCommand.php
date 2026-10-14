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

use Aws\CloudWatch\CloudWatchClient;
use Cbox\LaravelQueueAutoscale\LaravelQueueAutoscale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Publishes the autoscale cluster's worker demand against its capacity to CloudWatch, so ECS can scale the worker
 * service on queue pressure rather than CPU, which stays low while API-bound workers are starved.
 *
 * Only the direction of the deficit is reliable: its magnitude inherits every distortion in demand, so scaling
 * policies built on it should take small bounded steps and leave scale-in to a separate, slower signal.
 */
class PublishQueueScaleSignalCommand extends Command
{
    protected $signature = 'queue:autoscale:publish-scale-signal';

    protected $description = 'Publish the cluster worker demand-versus-capacity metrics to CloudWatch';

    public function handle(LaravelQueueAutoscale $autoscale, CloudWatchClient $cloudWatch): int
    {
        $summary = $autoscale->cluster();

        if ($summary === []) {
            $this->info('No cluster summary available; nothing published.');

            return self::SUCCESS;
        }

        $required = $summary['required_workers'] ?? null;
        $capacity = $summary['total_worker_capacity'] ?? null;

        if (! is_numeric($required) || ! is_numeric($capacity)) {
            $this->error('Cluster summary is missing required_workers or total_worker_capacity; nothing published.');

            return self::FAILURE;
        }

        $required = (int) $required;
        $capacity = (int) $capacity;
        $deficit = max(0, $required - $capacity);

        // No dimensions: each environment has its own AWS account, which already separates the series.
        $cloudWatch->putMetricData([
            'Namespace' => Config::string('app.queue-autoscale-metrics.namespace'),
            'MetricData' => [
                ['MetricName' => 'RequiredWorkers', 'Value' => $required, 'Unit' => 'Count'],
                ['MetricName' => 'WorkerCapacity', 'Value' => $capacity, 'Unit' => 'Count'],
                ['MetricName' => 'WorkerCapacityDeficit', 'Value' => $deficit, 'Unit' => 'Count'],
            ],
        ]);

        $this->info("Published required={$required} capacity={$capacity} deficit={$deficit}.");

        return self::SUCCESS;
    }
}
