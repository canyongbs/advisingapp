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

use App\Jobs\PruneQueueMonitorJob;
use Carbon\CarbonImmutable;
use Cbox\LaravelQueueMonitor\Enums\JobStatus;
use Cbox\LaravelQueueMonitor\Models\JobMonitor;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // The test suite runs with the queue monitor disabled.
    config(['queue-monitor.enabled' => true]);
});

function jobMonitorAged(JobStatus $status, CarbonImmutable $at): JobMonitor
{
    return JobMonitor::factory()->create([
        'status' => $status,
        'queued_at' => $at,
        'started_at' => $status === JobStatus::QUEUED ? null : $at,
        'created_at' => $at,
    ]);
}

function insertScalingEvent(string $reason, CarbonImmutable $createdAt): void
{
    DB::table('queue_monitor_scaling_events')->insert([
        'connection' => 'sqs',
        'queue' => 'default',
        'action' => 'scale_up',
        'current_workers' => 1,
        'target_workers' => 2,
        'reason' => $reason,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}

function insertClusterEvent(string $leaderId, CarbonImmutable $createdAt): void
{
    DB::table('queue_monitor_cluster_events')->insert([
        'cluster_id' => 'test',
        'event_type' => 'summary_published',
        'leader_id' => $leaderId,
        'meta' => json_encode(['leader' => $leaderId]),
        'created_at' => $createdAt,
    ]);
}

it('prunes finished jobs past the retention window and keeps recent ones', function () {
    $old = jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now()->subDays(20));
    $recent = jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now());

    (new PruneQueueMonitorJob())->handle();

    expect(JobMonitor::query()->whereKey($old->getKey())->exists())->toBeFalse()
        ->and(JobMonitor::query()->whereKey($recent->getKey())->exists())->toBeTrue();
});

it('marks a job stuck processing as timed out', function () {
    $stuck = jobMonitorAged(JobStatus::PROCESSING, CarbonImmutable::now()->subMinutes(400));
    $running = jobMonitorAged(JobStatus::PROCESSING, CarbonImmutable::now());

    (new PruneQueueMonitorJob())->handle();

    expect($stuck->refresh()->status)->toBe(JobStatus::TIMEOUT)
        ->and($running->refresh()->status)->toBe(JobStatus::PROCESSING);
});

it('records a completion time and duration on jobs it marks as timed out', function () {
    $stuck = jobMonitorAged(JobStatus::PROCESSING, CarbonImmutable::now()->subMinutes(400));

    (new PruneQueueMonitorJob())->handle();

    $stuck->refresh();

    expect($stuck->completed_at)->not->toBeNull()
        ->and($stuck->duration_ms)->toBeGreaterThanOrEqual(400 * 60 * 1000);
});

it('does not read job rows one at a time while resolving and pruning', function () {
    JobMonitor::query()->delete();

    foreach (range(1, 6) as $minutes) {
        jobMonitorAged(JobStatus::PROCESSING, CarbonImmutable::now()->subMinutes(400 + $minutes));
        jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now()->subDays(20 + $minutes));
    }

    DB::connection((new JobMonitor())->getConnectionName())->enableQueryLog();

    (new PruneQueueMonitorJob())->handle();

    // Any SELECT reading job rows or ids; the row-cap COUNT and the id subqueries inside DELETE statements are allowed.
    $rowLookups = collect(DB::connection((new JobMonitor())->getConnectionName())->getQueryLog())
        ->filter(fn (array $query): bool => str_starts_with($query['query'], 'select')
            && str_contains($query['query'], 'from "queue_monitor_jobs"')
            && ! str_starts_with($query['query'], 'select count('));

    expect($rowLookups->count())->toBe(0)
        ->and(JobMonitor::query()->where('status', JobStatus::TIMEOUT)->count())->toBe(6)
        ->and(JobMonitor::query()->where('status', JobStatus::COMPLETED)->count())->toBe(0);
});

it('deletes the oldest finished jobs once the row cap is exceeded', function () {
    JobMonitor::query()->delete();

    config(['queue-monitor.retention.max_rows' => 2]);

    $oldest = jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now()->subDays(3));
    $middle = jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now()->subDays(2));
    $newest = jobMonitorAged(JobStatus::COMPLETED, CarbonImmutable::now()->subDay());

    (new PruneQueueMonitorJob())->handle();

    expect(JobMonitor::query()->whereKey($oldest->getKey())->exists())->toBeFalse()
        ->and(JobMonitor::query()->whereKey($middle->getKey())->exists())->toBeTrue()
        ->and(JobMonitor::query()->whereKey($newest->getKey())->exists())->toBeTrue();
});

it('keeps old queued jobs that may still be waiting in a backlog', function () {
    $queued = jobMonitorAged(JobStatus::QUEUED, CarbonImmutable::now()->subDays(20));

    (new PruneQueueMonitorJob())->handle();

    expect(JobMonitor::query()->whereKey($queued->getKey())->exists())->toBeTrue();
});

it('prunes scaling events past the retention window and keeps recent ones', function () {
    insertScalingEvent('old', CarbonImmutable::now()->subDays(20));
    insertScalingEvent('recent', CarbonImmutable::now());

    (new PruneQueueMonitorJob())->handle();

    expect(DB::table('queue_monitor_scaling_events')->pluck('reason')->all())->toBe(['recent']);
});

it('nulls cluster event payloads past the payload window while keeping the rows', function () {
    config(['queue-monitor.retention.payload_days' => 2]);

    insertClusterEvent('old', CarbonImmutable::now()->subDays(3));
    insertClusterEvent('recent', CarbonImmutable::now());

    (new PruneQueueMonitorJob())->handle();

    expect(DB::table('queue_monitor_cluster_events')->count())->toBe(2)
        ->and(DB::table('queue_monitor_cluster_events')->where('leader_id', 'old')->value('meta'))->toBeNull()
        ->and(DB::table('queue_monitor_cluster_events')->where('leader_id', 'recent')->value('meta'))->not->toBeNull();
});
