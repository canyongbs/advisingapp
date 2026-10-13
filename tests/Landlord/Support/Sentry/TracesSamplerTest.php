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

use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use App\Jobs\DispatchHealthChecksForEachTenant;
use App\Jobs\LoadTestJob;
use App\Jobs\PruneModels;
use App\Jobs\RunHealthChecks;
use App\Jobs\SeedTenantDatabase;
use App\Overrides\Filament\Actions\Imports\Jobs\ImportCsvOverride;
use App\Support\Sentry\TracesSampler;
use Sentry\Tracing\SamplingContext;
use Sentry\Tracing\TransactionContext;

$samplingContext = function (string $operation, string $name, ?bool $parentSampled = null): SamplingContext {
    $transaction = new TransactionContext($name, $parentSampled);
    $transaction->setOp($operation);

    return SamplingContext::getDefault($transaction);
};

it('samples jobs by how long and how often they run', function (string $job, float $rate) use ($samplingContext) {
    expect(TracesSampler::sample($samplingContext('queue.process', $job)))->toBe($rate);
})->with([
    'a job with a long timeout' => [SeedTenantDatabase::class, 1.0],
    'an import job' => [ImportCsvOverride::class, 1.0],
    'a high volume job' => [RunHealthChecks::class, 0.01],
    'a high volume job with a short timeout' => [LookupPhoneNumber::class, 0.01],
    'a per-tenant orchestrator' => [DispatchHealthChecksForEachTenant::class, 0.01],
    'a job without its own timeout' => [PruneModels::class, 0.1],
    'a job that is not a class' => ['Closure', 0.1],
    'a load test job' => [LoadTestJob::class, 0.0],
]);

it('samples requests by path', function (string $path, float $rate) use ($samplingContext) {
    expect(TracesSampler::sample($samplingContext('http.server', $path)))->toBe($rate);
})->with([
    'a page' => ['/admin/students', 0.05],
    'a Livewire update' => ['/livewire/update', 0.01],
    'the uptime check' => ['/up', 0.0],
    'the health check' => ['/api/health', 0.0],
]);

it('keeps everything under a sampled parent in the trace', function () use ($samplingContext) {
    expect(TracesSampler::sample($samplingContext('queue.process', LoadTestJob::class, true)))->toBe(1.0)
        ->and(TracesSampler::sample($samplingContext('http.server', '/up', true)))->toBe(1.0);
});

it('does not trace other kinds of transaction', function () use ($samplingContext) {
    expect(TracesSampler::sample($samplingContext('console.command', 'migrate')))->toBe(0.0);
});

it('takes its rates from config', function () use ($samplingContext) {
    config([
        'sentry-sampling.rates.jobs' => 0.5,
        'sentry-sampling.rates.requests' => 0.25,
    ]);

    expect(TracesSampler::sample($samplingContext('queue.process', PruneModels::class)))->toBe(0.5)
        ->and(TracesSampler::sample($samplingContext('http.server', '/admin/students')))->toBe(0.25);
});
