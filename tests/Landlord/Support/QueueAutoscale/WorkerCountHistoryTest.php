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
use App\Support\QueueAutoscale\WorkerCountHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    config(['cache.stores.landlord' => ['driver' => 'array']]);
    Cache::purge('landlord');
});

it('records and returns a per-queue worker count series', function () {
    $history = app(WorkerCountHistory::class);

    $history->record('outbound-communication', 3, 1000);
    $history->record('outbound-communication', 7, 1060);
    $history->record('default', 1, 1000);

    expect($history->series('outbound-communication'))->toBe([
        ['timestamp' => 1000, 'count' => 3],
        ['timestamp' => 1060, 'count' => 7],
    ])
        ->and($history->series('default'))->toBe([
            ['timestamp' => 1000, 'count' => 1],
        ])
        ->and($history->series('never-sampled'))->toBe([]);
});

it('drops samples older than the retention window', function () {
    $history = app(WorkerCountHistory::class);

    $now = 1_000_000;

    $history->record('default', 1, $now - 90_000);
    $history->record('default', 5, $now);

    expect($history->series('default'))->toBe([
        ['timestamp' => $now, 'count' => 5],
    ]);
});

it('registers sampled queues and ages stale ones out of the queue registry', function () {
    $history = app(WorkerCountHistory::class);

    $now = now()->getTimestamp();

    $history->record('default', 4, $now);
    $history->record('stale-queue', 2, $now - 90_000);

    expect($history->queues())->toBe(['default']);
});

it('aligns per-queue counts onto one sorted timeline with gaps as null', function () {
    $history = app(WorkerCountHistory::class);

    $history->record('outbound-communication', 3, 1000);
    $history->record('outbound-communication', 7, 1120);
    $history->record('default', 1, 1000);

    expect($history->alignedSeries(['default', 'outbound-communication']))->toBe([
        'timestamps' => [1000, 1120],
        'counts' => [
            'default' => [1, null],
            'outbound-communication' => [3, 7],
        ],
    ]);
});

it('shares the series between tenants', function () {
    // A file store, unlike the array store, survives the cache manager being rebuilt when the tenant switches.
    $path = storage_path('framework/testing/' . Str::uuid());
    config(['cache.stores.landlord' => ['driver' => 'file', 'path' => $path]]);
    Cache::purge('landlord');

    try {
        Tenant::query()->first()->execute(fn () => app(WorkerCountHistory::class)->record('default', 6));

        Tenant::forgetCurrent();

        expect(app(WorkerCountHistory::class)->latest('default'))->toBe(6);
    } finally {
        File::deleteDirectory($path);
    }
});
