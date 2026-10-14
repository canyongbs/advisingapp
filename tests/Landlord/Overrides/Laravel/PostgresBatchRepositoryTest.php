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

use App\Models\Tenant;
use App\Overrides\Laravel\PostgresBatchRepository;
use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

function storedBatch(): Batch
{
    return app(BatchRepository::class)->store(new PendingBatch(app(), new Collection()));
}

it('resolves the batch repository to the Postgres implementation', function () {
    expect(app(BatchRepository::class))->toBeInstanceOf(PostgresBatchRepository::class)
        ->and(app(DatabaseBatchRepository::class))->toBeInstanceOf(PostgresBatchRepository::class);
});

it("follows the tenant's batching connection", function () {
    $tenant = Tenant::query()->first();

    $connectionName = $tenant->execute(fn (): ?string => app(BatchRepository::class)->getConnection()->getName());

    expect($connectionName)->toBe(config('multitenancy.tenant_database_connection_name'));
});

it('decrements pending jobs and returns the post-update counts', function () {
    $repository = app(BatchRepository::class);

    $batch = storedBatch();

    $repository->incrementTotalJobs($batch->id, 2);

    $counts = $repository->decrementPendingJobs($batch->id, 'job-1');

    expect($counts->pendingJobs)->toBe(1)
        ->and($counts->failedJobs)->toBe(0);

    $counts = $repository->decrementPendingJobs($batch->id, 'job-2');

    expect($counts->pendingJobs)->toBe(0);

    $fresh = $repository->find($batch->id);

    expect($fresh?->pendingJobs)->toBe(0)
        ->and($fresh?->totalJobs)->toBe(2)
        ->and($fresh?->failedJobIds)->toBe([]);
});

it('records a failed job id once while counting every failure', function () {
    $repository = app(BatchRepository::class);

    $batch = storedBatch();

    $repository->incrementTotalJobs($batch->id, 1);

    $counts = $repository->incrementFailedJobs($batch->id, 'job-1');

    expect($counts->failedJobs)->toBe(1);

    $repository->incrementFailedJobs($batch->id, 'job-1');

    $fresh = $repository->find($batch->id);

    expect($fresh?->failedJobs)->toBe(2)
        ->and($fresh?->failedJobIds)->toBe(['job-1']);
});

it('removes a failed job id when the job completes on retry', function () {
    $repository = app(BatchRepository::class);

    $batch = storedBatch();

    $repository->incrementTotalJobs($batch->id, 2);
    $repository->incrementFailedJobs($batch->id, 'job-1');
    $repository->incrementFailedJobs($batch->id, 'job-2');

    $repository->decrementPendingJobs($batch->id, 'job-1');

    expect($repository->find($batch->id)?->failedJobIds)->toBe(['job-2']);
});

it('leaves the failed job ids untouched when an unlisted job completes', function () {
    $repository = app(BatchRepository::class);

    $batch = storedBatch();

    $repository->incrementTotalJobs($batch->id, 2);
    $repository->incrementFailedJobs($batch->id, 'job-1');

    $repository->decrementPendingJobs($batch->id, 'job-2');

    $fresh = $repository->find($batch->id);

    expect($fresh?->failedJobIds)->toBe(['job-1'])
        ->and($fresh?->pendingJobs)->toBe(1)
        ->and($fresh?->failedJobs)->toBe(1);
});

it('returns zero counts for a batch that does not exist', function () {
    $counts = app(BatchRepository::class)->decrementPendingJobs((string) Str::uuid(), 'job-1');

    expect($counts->pendingJobs)->toBe(0)
        ->and($counts->failedJobs)->toBe(0);
});
