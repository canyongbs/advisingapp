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

use App\Actions\QueueMonitoring\RetryFailedJobs;
use App\Filament\Widgets\QueueMonitoring\TenantFailedJobsTable;
use App\Models\FailedJob;
use App\Models\Tenant;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

function tenantFailedJobOn(?string $connection = null): FailedJob
{
    return FailedJob::on($connection)->forceCreate([
        'uuid' => (string) Str::uuid(),
        'connection' => 'sqs',
        'queue' => 'default',
        'payload' => json_encode(['displayName' => 'App\\Jobs\\ExampleJob']),
        'exception' => "RuntimeException: Failed.\n#0 stack trace",
        'failed_at' => now(),
    ]);
}

it("lists only the current tenant's failed jobs under the tenant's name", function () {
    asSuperAdmin();

    $tenantFailedJob = tenantFailedJobOn();
    $landlordFailedJob = tenantFailedJobOn('landlord');

    $component = Livewire::test(TenantFailedJobsTable::class)
        ->assertCanSeeTableRecords([$tenantFailedJob])
        ->assertTableColumnStateSet('display_name', 'App\\Jobs\\ExampleJob', record: $tenantFailedJob)
        ->assertTableColumnFormattedStateSet('exception', 'RuntimeException: Failed.', record: $tenantFailedJob)
        ->assertCountTableRecords(1);

    expect($component->instance()->getTable()->getHeading())->toBe('Failed Jobs: ' . Tenant::current()->name)
        ->and(FailedJob::on('landlord')->whereKey($landlordFailedJob->getKey())->exists())->toBeTrue();
});

it("retries a failed job in the current tenant's context", function () {
    asSuperAdmin();

    $failedJob = tenantFailedJobOn();

    $retryFailedJobs = Mockery::mock(RetryFailedJobs::class);
    $retryFailedJobs->shouldReceive('__invoke')
        ->once()
        ->withArgs(fn (array $failedJobIds, ?Tenant $tenant): bool => $failedJobIds === [$failedJob->uuid] && $tenant?->is(Tenant::current()));
    app()->instance(RetryFailedJobs::class, $retryFailedJobs);

    Livewire::test(TenantFailedJobsTable::class)
        ->callAction(TestAction::make('retry')->table($failedJob))
        ->assertNotified('Job queued for retry.');
});

it('retries the selected failed jobs together', function () {
    asSuperAdmin();

    $failedJobs = collect([tenantFailedJobOn(), tenantFailedJobOn()]);

    $retryFailedJobs = Mockery::mock(RetryFailedJobs::class);
    $retryFailedJobs->shouldReceive('__invoke')
        ->once()
        ->withArgs(fn (array $failedJobIds, ?Tenant $tenant): bool => collect($failedJobIds)->sort()->values()->all() === $failedJobs->pluck('uuid')->sort()->values()->all()
            && $tenant?->is(Tenant::current()));
    app()->instance(RetryFailedJobs::class, $retryFailedJobs);

    Livewire::test(TenantFailedJobsTable::class)
        ->selectTableRecords($failedJobs)
        ->callAction(TestAction::make('retry')->table()->bulk())
        ->assertNotified('2 jobs queued for retry.');
});

it("forgets a failed job from the current tenant's failed jobs", function () {
    asSuperAdmin();

    $failedJob = tenantFailedJobOn();

    expect(FailedJob::query()->whereKey($failedJob->getKey())->exists())->toBeTrue();

    Livewire::test(TenantFailedJobsTable::class)
        ->callAction(TestAction::make('forget')->table($failedJob))
        ->assertNotified('Failed job removed.');

    expect(FailedJob::query()->whereKey($failedJob->getKey())->exists())->toBeFalse();
});

it('shows the full exception in the failed job details', function () {
    asSuperAdmin();

    $failedJob = tenantFailedJobOn();

    Livewire::test(TenantFailedJobsTable::class)
        ->mountAction(TestAction::make('details')->table($failedJob))
        ->assertMountedActionModalSee([$failedJob->uuid, '#0 stack trace']);
});
