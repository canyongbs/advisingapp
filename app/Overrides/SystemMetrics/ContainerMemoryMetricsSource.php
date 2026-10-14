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

namespace App\Overrides\SystemMetrics;

use Cbox\SystemMetrics\Contracts\ContainerMetricsSource;
use Cbox\SystemMetrics\Contracts\MemoryMetricsSource;
use Cbox\SystemMetrics\DTO\Metrics\Memory\MemorySnapshot;
use Cbox\SystemMetrics\DTO\Result;
use Cbox\SystemMetrics\Sources\Container\CompositeContainerMetricsSource;
use Cbox\SystemMetrics\Sources\Memory\CompositeMemoryMetricsSource;
use Override;

/**
 * Reports memory against the container's own cgroup limit when it has one, rather than the whole host or VM, so the
 * autoscaler's used percentage is of the same limit as its capacity.
 * Revert once upstream measures it: docs/explanations/oss-todo/cbox-queue-autoscale-memory-capacity.md
 */
final readonly class ContainerMemoryMetricsSource implements MemoryMetricsSource
{
    public function __construct(
        private MemoryMetricsSource $hostSource = new CompositeMemoryMetricsSource(),
        private ContainerMetricsSource $containerSource = new CompositeContainerMetricsSource(),
    ) {}

    /**
     * @return Result<MemorySnapshot>
     */
    #[Override]
    public function read(): Result
    {
        $hostResult = $this->hostSource->read();

        if ($hostResult->isFailure()) {
            return $hostResult;
        }

        $containerResult = $this->containerSource->read();

        if ($containerResult->isFailure()) {
            return $hostResult;
        }

        $limit = $containerResult->getValue()->memoryLimitBytes;
        $usage = $containerResult->getValue()->memoryUsageBytes;

        if (($limit === null) || ($limit <= 0) || ($usage === null)) {
            return $hostResult;
        }

        $host = $hostResult->getValue();
        $used = min($usage, $limit);

        return Result::success(new MemorySnapshot(
            totalBytes: $limit,
            freeBytes: $limit - $used,
            availableBytes: $limit - $used,
            usedBytes: $used,
            buffersBytes: 0,
            cachedBytes: 0,
            swapTotalBytes: $host->swapTotalBytes,
            swapFreeBytes: $host->swapFreeBytes,
            swapUsedBytes: $host->swapUsedBytes,
        ));
    }
}
