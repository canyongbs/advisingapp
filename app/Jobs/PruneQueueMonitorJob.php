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

namespace App\Jobs;

use App\Features\QueueMonitoringFeature;
use Cbox\LaravelQueueMonitor\Actions\Core\PruneJobsAction;
use Closure;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

/**
 * Runs queue monitor retention on a worker rather than in the scheduler: deleting from queue_monitor_jobs touches
 * many indexes and can run for minutes on a backlog. ShouldBeUnique keeps one run in flight, and a timeout or failure
 * is the signal that retention has fallen behind.
 */
class PruneQueueMonitorJob implements ShouldBeUnique, ShouldQueue, NotTenantAware
{
    use Queueable;

    private const int JOB_RETENTION_DAYS = 14;

    private const int EVENT_RETENTION_DAYS = 7;

    private const int EVENT_MAX_ROWS = 250_000;

    private const int BATCH_SIZE = 5_000;

    // Below the 1200s SQS visibility timeout, so a run still in progress is never handed to a second worker.
    public int $timeout = 900;

    public int $tries = 1;

    public bool $failOnTimeout = true;

    public int $uniqueFor = 1200;

    public function __construct()
    {
        $this->onQueue(config('queue.landlord_queue'));
    }

    public function handle(): void
    {
        // TODO: Cleanup Task (queue-monitoring): remove this check; the tables may not exist before the flag is active.
        if (! QueueMonitoringFeature::active()) {
            return;
        }

        // Marks jobs stuck in `processing` (their worker was killed mid-job) as timed out, so the prune below reclaims
        // them. Live `queued` rows are never reclaimed: an old one may still be waiting in a deep backlog.
        Artisan::call('queue-monitor:resolve-stuck');

        app(PruneJobsAction::class)->execute(self::JOB_RETENTION_DAYS);

        // The package prunes each event table in one unbounded statement, which deadlocks against the autoscale
        // listeners' inserts. Prune each table in batches and independently, so one failing never blocks the other.
        $failure = null;

        foreach (['scaling_events', 'cluster_events'] as $table) {
            try {
                $this->pruneEventTable($table);
            } catch (Throwable $exception) {
                if ($failure === null) {
                    $failure = $exception;
                } else {
                    report($exception);
                }
            }
        }

        if ($failure !== null) {
            throw $failure;
        }
    }

    private function pruneEventTable(string $tableSuffix): void
    {
        $connection = config('queue-monitor.database.connection');
        assert($connection === null || is_string($connection));

        $prefix = config('queue-monitor.database.table_prefix', 'queue_monitor_');
        assert(is_string($prefix));

        $table = $prefix . $tableSuffix;

        // Only cluster_events carries a `meta` payload; it is nulled after payload_days while the row is kept.
        $payloadDays = config('queue-monitor.retention.payload_days');

        if (is_int($payloadDays) && Schema::connection($connection)->hasColumn($table, 'meta')) {
            $payloadBoundaryId = $this->boundaryId($connection, $table, fn (Builder $query): Builder => $query->where('created_at', '<', now()->subDays($payloadDays)));

            if ($payloadBoundaryId !== null) {
                $this->nullPayloadsUpToId($connection, $table, $payloadBoundaryId);
            }
        }

        $ageBoundaryId = $this->boundaryId($connection, $table, fn (Builder $query): Builder => $query->where('created_at', '<', now()->subDays(self::EVENT_RETENTION_DAYS)));

        if ($ageBoundaryId !== null) {
            $this->deleteUpToId($connection, $table, $ageBoundaryId);
        }

        $capBoundaryId = $this->boundaryId($connection, $table, fn (Builder $query): Builder => $query->offset(self::EVENT_MAX_ROWS));

        if ($capBoundaryId !== null) {
            $this->deleteUpToId($connection, $table, $capBoundaryId);
        }
    }

    /**
     * The largest id matching the constraint, resolved once so the batches below run off the primary key instead of
     * rescanning the unindexed `created_at`.
     *
     * @param  Closure(Builder): Builder  $constrain
     */
    private function boundaryId(?string $connection, string $table, Closure $constrain): ?int
    {
        $value = $constrain(DB::connection($connection)->table($table)->orderByDesc('id'))
            ->limit(1)
            ->value('id');

        if ($value === null) {
            return null;
        }

        assert(is_numeric($value));

        return (int) $value;
    }

    private function deleteUpToId(?string $connection, string $table, int $boundaryId): void
    {
        do {
            $ids = DB::connection($connection)->table($table)
                ->where('id', '<=', $boundaryId)
                ->orderBy('id')
                ->limit(self::BATCH_SIZE)
                ->pluck('id');

            if ($ids->isEmpty()) {
                return;
            }

            DB::connection($connection)->table($table)->whereIn('id', $ids->all())->delete();
        } while ($ids->count() === self::BATCH_SIZE);
    }

    private function nullPayloadsUpToId(?string $connection, string $table, int $boundaryId): void
    {
        do {
            $ids = DB::connection($connection)->table($table)
                ->where('id', '<=', $boundaryId)
                ->whereNotNull('meta')
                ->orderBy('id')
                ->limit(self::BATCH_SIZE)
                ->pluck('id');

            if ($ids->isEmpty()) {
                return;
            }

            DB::connection($connection)->table($table)->whereIn('id', $ids->all())->update(['meta' => null]);
        } while ($ids->count() === self::BATCH_SIZE);
    }
}
