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

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

/**
 * The metrics package only exposes a point-in-time worker count per queue, so this samples it into a rolling per-queue
 * series for the worker count chart. It uses the `landlord` cache store so the series is the same in every tenant.
 */
class WorkerCountHistory
{
    private const string CACHE_KEY_PREFIX = 'queue-worker-count-history:';

    private const string QUEUES_CACHE_KEY = 'queue-worker-count-history-queues';

    private const int RETENTION_SECONDS = 86400;

    public function record(string $queue, int $count, ?int $timestamp = null): void
    {
        $timestamp ??= Date::now()->getTimestamp();

        $series = $this->series($queue);
        $series[] = ['timestamp' => $timestamp, 'count' => $count];

        $this->cache()->put(
            self::cacheKey($queue),
            $this->trim($series, $timestamp),
            self::RETENTION_SECONDS + 3600,
        );

        $this->registerQueue($queue, $timestamp);
    }

    /**
     * Queue names with at least one sample inside the retention window.
     *
     * @return list<string>
     */
    public function queues(): array
    {
        $registry = $this->cache()->get(self::QUEUES_CACHE_KEY, []);

        if (! is_array($registry)) {
            return [];
        }

        $cutoff = Date::now()->getTimestamp() - self::RETENTION_SECONDS;
        $queues = [];

        foreach ($registry as $queue => $lastSampledAt) {
            if (is_string($queue) && is_numeric($lastSampledAt) && (int) $lastSampledAt >= $cutoff) {
                $queues[] = $queue;
            }
        }

        return $queues;
    }

    /**
     * @return list<array{timestamp: int, count: int}>
     */
    public function series(string $queue): array
    {
        $stored = $this->cache()->get(self::cacheKey($queue), []);

        if (! is_array($stored)) {
            return [];
        }

        $series = [];

        foreach ($stored as $point) {
            if (is_array($point) && is_numeric($point['timestamp'] ?? null) && is_numeric($point['count'] ?? null)) {
                $series[] = ['timestamp' => (int) $point['timestamp'], 'count' => (int) $point['count']];
            }
        }

        return $series;
    }

    public function latest(string $queue): ?int
    {
        $series = $this->series($queue);

        if ($series === []) {
            return null;
        }

        return $series[array_key_last($series)]['count'];
    }

    /**
     * Aligns every queue's samples onto one sorted timeline so each queue becomes a line of equal length, with null
     * where that queue had no sample.
     *
     * @param  list<string>  $queues
     *
     * @return array{timestamps: list<int>, counts: array<string, list<int|null>>}
     */
    public function alignedSeries(array $queues): array
    {
        $pointsByQueue = [];
        $timestamps = [];

        foreach ($queues as $queue) {
            $points = [];

            foreach ($this->series($queue) as $point) {
                $points[$point['timestamp']] = $point['count'];
                $timestamps[$point['timestamp']] = true;
            }

            $pointsByQueue[$queue] = $points;
        }

        $sortedTimestamps = array_keys($timestamps);
        sort($sortedTimestamps);

        $counts = [];

        foreach ($pointsByQueue as $queue => $points) {
            $counts[$queue] = array_map(
                fn (int $timestamp): ?int => $points[$timestamp] ?? null,
                $sortedTimestamps,
            );
        }

        return ['timestamps' => $sortedTimestamps, 'counts' => $counts];
    }

    /**
     * @param  list<array{timestamp: int, count: int}>  $series
     *
     * @return list<array{timestamp: int, count: int}>
     */
    private function trim(array $series, int $now): array
    {
        $cutoff = $now - self::RETENTION_SECONDS;

        return array_values(array_filter(
            $series,
            fn (array $point): bool => $point['timestamp'] >= $cutoff,
        ));
    }

    private function registerQueue(string $queue, int $timestamp): void
    {
        $registry = $this->cache()->get(self::QUEUES_CACHE_KEY, []);

        if (! is_array($registry)) {
            $registry = [];
        }

        $registry[$queue] = $timestamp;

        $cutoff = $timestamp - self::RETENTION_SECONDS;

        $registry = array_filter(
            $registry,
            fn (mixed $lastSampledAt): bool => is_numeric($lastSampledAt) && (int) $lastSampledAt >= $cutoff,
        );

        $this->cache()->put(self::QUEUES_CACHE_KEY, $registry, self::RETENTION_SECONDS + 3600);
    }

    private function cache(): Repository
    {
        return Cache::store('landlord');
    }

    private static function cacheKey(string $queue): string
    {
        return self::CACHE_KEY_PREFIX . $queue;
    }
}
