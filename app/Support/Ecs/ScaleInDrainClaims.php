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

namespace App\Support\Ecs;

use Carbon\CarbonImmutable;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

/**
 * Decides which worker tasks drain when scale-in wants fewer tasks but every task is protected, so ECS cannot pick one.
 *
 * Each task records itself and its protection expiry, and the tasks whose protection runs out soonest (the least busy)
 * claim the drain slots. Claims beyond what scale-in still needs are given back, latest expiry first.
 */
class ScaleInDrainClaims
{
    // Several missed checks, so a task that stopped reporting no longer holds a slot or counts as a candidate.
    private const int STALE_AFTER_SECONDS = 60;

    private const int RETENTION_SECONDS = 600;

    private const int LOCK_SECONDS = 10;

    private const int LOCK_WAIT_SECONDS = 5;

    /**
     * Returns whether this task should be draining for scale-in.
     *
     * @param ?list<string> $runningTaskArns Null when not listed, because ECS is not running more tasks than it wants.
     */
    public function reconcile(
        EcsTaskIdentity $task,
        ?CarbonImmutable $protectedUntil,
        ?array $runningTaskArns,
        int $excess,
        bool $isClaimed,
    ): bool {
        $key = "ecs-worker-drain-claims:{$task->cluster}:{$task->serviceName}";

        $shouldDrain = $this->lock("{$key}:lock")->block(
            self::LOCK_WAIT_SECONDS,
            function () use ($key, $task, $protectedUntil, $runningTaskArns, $excess, $isClaimed): bool {
                $now = Date::now()->getTimestamp();

                $entries = $this->liveEntries($key, $runningTaskArns, $now);
                $entries[$task->taskArn] = [
                    'protected_until' => $protectedUntil?->getTimestamp() ?? 0,
                    'seen_at' => $now,
                    'claimed' => $isClaimed,
                ];

                $claimed = array_filter($entries, fn (array $entry): bool => $entry['claimed']);
                $excess = max(0, $excess);

                if ($isClaimed) {
                    uasort($claimed, fn (array $first, array $second): int => $second['protected_until'] <=> $first['protected_until']);

                    $surplusClaims = array_slice(array_keys($claimed), 0, max(0, count($claimed) - $excess));
                    $shouldDrain = ! in_array($task->taskArn, $surplusClaims, strict: true);
                } else {
                    $unclaimed = array_filter($entries, fn (array $entry): bool => ! $entry['claimed']);

                    uksort($unclaimed, fn (string $first, string $second): int => [$unclaimed[$first]['protected_until'], $first] <=> [$unclaimed[$second]['protected_until'], $second]);

                    $chosen = array_slice(array_keys($unclaimed), 0, max(0, $excess - count($claimed)));
                    $shouldDrain = in_array($task->taskArn, $chosen, strict: true);
                }

                $entries[$task->taskArn]['claimed'] = $shouldDrain;

                $this->cache()->put($key, $entries, self::RETENTION_SECONDS);

                return $shouldDrain;
            },
        );

        assert(is_bool($shouldDrain));

        return $shouldDrain;
    }

    /**
     * @param ?list<string> $runningTaskArns
     *
     * @return array<string, array{protected_until: int, seen_at: int, claimed: bool}>
     */
    private function liveEntries(string $key, ?array $runningTaskArns, int $now): array
    {
        $stored = $this->cache()->get($key, []);
        $entries = [];

        if (! is_array($stored)) {
            return $entries;
        }

        foreach ($stored as $taskArn => $entry) {
            if (
                (! is_string($taskArn))
                || (($runningTaskArns !== null) && (! in_array($taskArn, $runningTaskArns, strict: true)))
                || (! is_array($entry))
                || (! is_int($entry['protected_until'] ?? null))
                || (! is_int($entry['seen_at'] ?? null))
                || (! is_bool($entry['claimed'] ?? null))
                || ($entry['seen_at'] < ($now - self::STALE_AFTER_SECONDS))
            ) {
                continue;
            }

            $entries[$taskArn] = [
                'protected_until' => $entry['protected_until'],
                'seen_at' => $entry['seen_at'],
                'claimed' => $entry['claimed'],
            ];
        }

        return $entries;
    }

    private function cache(): Repository
    {
        return Cache::store('landlord');
    }

    private function lock(string $name): Lock
    {
        $repository = $this->cache();

        assert($repository instanceof CacheRepository);

        $store = $repository->getStore();

        assert($store instanceof LockProvider);

        return $store->lock($name, self::LOCK_SECONDS);
    }
}
