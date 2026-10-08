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

use App\Filament\Widgets\QueueMonitoring\QueueJobsTable;
use App\Models\Tenant;
use App\Support\QueueAutoscale\WorkerCountHistory;
use Cbox\LaravelQueueMonitor\Models\JobMonitor;
use Illuminate\Support\Facades\Cache;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    config(['cache.stores.landlord' => ['driver' => 'array']]);
    Cache::purge('landlord');
});

it('lists monitored jobs', function () {
    asSuperAdmin();

    $jobs = JobMonitor::factory()->count(3)->create();

    livewire(QueueJobsTable::class)
        ->assertCanSeeTableRecords($jobs);
});

it('hides jobs from before the last day until the date filter is cleared', function () {
    asSuperAdmin();

    $recentJob = JobMonitor::factory()->create(['created_at' => now()->subHour()]);
    $oldJob = JobMonitor::factory()->create(['created_at' => now()->subDays(2)]);

    livewire(QueueJobsTable::class)
        ->assertCanSeeTableRecords([$recentJob])
        ->assertCanNotSeeTableRecords([$oldJob])
        ->filterTable('createdAfter', ['value' => null])
        ->assertCanSeeTableRecords([$recentJob, $oldJob]);
});

it('falls back to the job class when a job has no display name', function () {
    asSuperAdmin();

    $job = JobMonitor::factory()->create(['display_name' => null, 'job_class' => 'App\\Jobs\\ExampleJob']);

    livewire(QueueJobsTable::class)
        ->assertTableColumnStateSet('display_name', 'App\\Jobs\\ExampleJob', record: $job);
});

it('shows the tenant each job ran for', function () {
    asSuperAdmin();

    $tenant = Tenant::current();

    $tenantJob = JobMonitor::factory()->create();
    $landlordJob = tap(JobMonitor::factory()->create(), fn (JobMonitor $job) => $job->forceFill(['tenant_id' => null])->save());

    livewire(QueueJobsTable::class)
        ->assertTableColumnStateSet('tenant_id', $tenant->name, record: $tenantJob)
        ->assertTableColumnStateSet('tenant_id', 'Landlord', record: $landlordJob);
});

it('filters jobs by tenant', function () {
    asSuperAdmin();

    $tenant = Tenant::current();

    $tenantJob = JobMonitor::factory()->create();
    $landlordJob = tap(JobMonitor::factory()->create(), fn (JobMonitor $job) => $job->forceFill(['tenant_id' => null])->save());

    livewire(QueueJobsTable::class)
        ->filterTable('tenant', $tenant->getKey())
        ->assertCanSeeTableRecords([$tenantJob])
        ->assertCanNotSeeTableRecords([$landlordJob])
        ->filterTable('tenant', 'landlord')
        ->assertCanSeeTableRecords([$landlordJob])
        ->assertCanNotSeeTableRecords([$tenantJob]);
});

it('filters jobs by a known queue', function () {
    config(['queue.queues' => ['advisingapp-default']]);
    app(WorkerCountHistory::class)->record('ad-hoc', 1);

    asSuperAdmin();

    $adHocJob = JobMonitor::factory()->create(['queue' => 'ad-hoc']);
    $defaultJob = JobMonitor::factory()->create(['queue' => 'advisingapp-default']);

    livewire(QueueJobsTable::class)
        ->filterTable('queue', 'ad-hoc')
        ->assertCanSeeTableRecords([$adHocJob])
        ->assertCanNotSeeTableRecords([$defaultJob]);
});
