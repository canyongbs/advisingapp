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

use Cbox\LaravelQueueAutoscale\Contracts\FailureWindowStoreContract;
use Cbox\LaravelQueueAutoscale\Support\WorkloadKey;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

/**
 * A copy of the package's CacheFailureWindowStore on the `landlord` cache store. Workers record job outcomes while the
 * job's tenant is current, where the default store is tenant-prefixed, but the autoscale manager reads them outside
 * any tenant, so both must use a store whose prefix does not change.
 */
class LandlordFailureWindowStore implements FailureWindowStoreContract
{
    private const int STATE_TTL_SECONDS = 86400;

    public function recordOutcome(string $connection, string $queue, bool $failed, int $windowSeconds): void
    {
        $bucket = $this->bucketStart($this->now(), $windowSeconds);
        $ttl = $windowSeconds * 2;

        $this->increment($this->counterKey($connection, $queue, $bucket, 'total'), $ttl);

        if ($failed) {
            $this->increment($this->counterKey($connection, $queue, $bucket, 'failures'), $ttl);
        }
    }

    public function currentWindow(string $connection, string $queue, int $windowSeconds): array
    {
        $current = $this->bucketStart($this->now(), $windowSeconds);
        $previous = $current - $windowSeconds;

        return [
            'total' => $this->read($this->counterKey($connection, $queue, $current, 'total'))
                + $this->read($this->counterKey($connection, $queue, $previous, 'total')),
            'failures' => $this->read($this->counterKey($connection, $queue, $current, 'failures'))
                + $this->read($this->counterKey($connection, $queue, $previous, 'failures')),
        ];
    }

    public function resetWindow(string $connection, string $queue, int $windowSeconds): void
    {
        $current = $this->bucketStart($this->now(), $windowSeconds);
        $previous = $current - $windowSeconds;

        foreach ([$current, $previous] as $bucket) {
            $this->cache()->forget($this->counterKey($connection, $queue, $bucket, 'total'));
            $this->cache()->forget($this->counterKey($connection, $queue, $bucket, 'failures'));
        }
    }

    public function readState(string $connection, string $queue): ?array
    {
        $raw = $this->cache()->get($this->stateKey($connection, $queue));

        if (! is_string($raw)) {
            return null;
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded) || ! isset($decoded['state'], $decoded['changed_at'])) {
            return null;
        }

        if (! is_string($decoded['state']) || ! is_numeric($decoded['changed_at'])) {
            return null;
        }

        return [
            'state' => $decoded['state'],
            'changed_at' => (float) $decoded['changed_at'],
        ];
    }

    public function writeState(string $connection, string $queue, string $state, float $changedAt): void
    {
        $this->cache()->put(
            $this->stateKey($connection, $queue),
            json_encode(['state' => $state, 'changed_at' => $changedAt], JSON_THROW_ON_ERROR),
            self::STATE_TTL_SECONDS,
        );
    }

    private function cache(): Repository
    {
        return Cache::store('landlord');
    }

    private function increment(string $key, int $ttl): void
    {
        $this->cache()->add($key, 0, $ttl);
        $this->cache()->increment($key);
    }

    private function read(string $key): int
    {
        $value = $this->cache()->get($key, 0);

        return is_numeric($value) ? (int) $value : 0;
    }

    private function now(): float
    {
        return microtime(true);
    }

    private function bucketStart(float $timestamp, int $windowSeconds): int
    {
        return intdiv((int) $timestamp, $windowSeconds) * $windowSeconds;
    }

    private function counterKey(string $connection, string $queue, int $bucket, string $suffix): string
    {
        return sprintf(
            'autoscale:fuse:window:%s:%s:%d:%s',
            WorkloadKey::label($queue),
            WorkloadKey::for($connection, $queue),
            $bucket,
            $suffix,
        );
    }

    private function stateKey(string $connection, string $queue): string
    {
        return sprintf(
            'autoscale:fuse:state:%s:%s',
            WorkloadKey::label($queue),
            WorkloadKey::for($connection, $queue),
        );
    }
}
