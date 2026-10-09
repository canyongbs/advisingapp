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

use App\Overrides\SystemMetrics\ContainerMemoryMetricsSource;
use Cbox\SystemMetrics\DTO\Metrics\Container\CgroupVersion;
use Cbox\SystemMetrics\DTO\Metrics\Container\ContainerLimits;
use Cbox\SystemMetrics\Exceptions\SystemMetricsException;
use Cbox\SystemMetrics\Testing\FakeContainerMetricsSource;
use Cbox\SystemMetrics\Testing\FakeMemoryMetricsSource;

function containerWithMemory(?int $limitBytes, ?int $usageBytes): FakeContainerMetricsSource
{
    return (new FakeContainerMetricsSource())->set(new ContainerLimits(
        cgroupVersion: CgroupVersion::V1,
        cpuQuota: null,
        memoryLimitBytes: $limitBytes,
        cpuUsageCores: null,
        memoryUsageBytes: $usageBytes,
        cpuThrottledCount: null,
        oomKillCount: null,
    ));
}

it('reports memory against the container limit when the container has one', function () {
    $source = new ContainerMemoryMetricsSource(new FakeMemoryMetricsSource(), containerWithMemory(4_294_967_296, 3_221_225_472));

    $snapshot = $source->read()->getValue();

    $host = FakeMemoryMetricsSource::default();

    expect($snapshot->totalBytes)->toBe(4_294_967_296)
        ->and($snapshot->usedBytes)->toBe(3_221_225_472)
        ->and($snapshot->availableBytes)->toBe(1_073_741_824)
        ->and($snapshot->usedPercentage())->toBe(75.0)
        ->and($snapshot->swapTotalBytes)->toBe($host->swapTotalBytes);
});

it('caps usage at the container limit', function () {
    $snapshot = (new ContainerMemoryMetricsSource(new FakeMemoryMetricsSource(), containerWithMemory(4_294_967_296, 5_000_000_000)))
        ->read()
        ->getValue();

    expect($snapshot->usedPercentage())->toBe(100.0)
        ->and($snapshot->availableBytes)->toBe(0);
});

it('reports the host memory when the container has no usable limit', function (FakeContainerMetricsSource $container) {
    $snapshot = (new ContainerMemoryMetricsSource(new FakeMemoryMetricsSource(), $container))->read()->getValue();

    expect($snapshot)->toEqual(FakeMemoryMetricsSource::default());
})->with([
    'no memory limit' => fn () => containerWithMemory(null, 3_221_225_472),
    'no memory usage' => fn () => containerWithMemory(4_294_967_296, null),
    'not in a container' => fn () => new FakeContainerMetricsSource(),
]);

it('passes a host memory failure through', function () {
    $host = (new FakeMemoryMetricsSource())->failWith(new SystemMetricsException('No /proc/meminfo'));

    $result = (new ContainerMemoryMetricsSource($host, containerWithMemory(4_294_967_296, 3_221_225_472)))->read();

    expect($result->isFailure())->toBeTrue();
});
