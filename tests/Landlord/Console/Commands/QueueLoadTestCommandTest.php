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

use App\Jobs\LoadTestJob;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\artisan;

it('dispatches the requested number of jobs onto the queue', function () {
    Queue::fake();

    artisan('queue:loadtest', ['queue' => 'import-export', 'count' => 3, '--duration' => 1])->assertSuccessful();

    Queue::assertPushedOn('import-export', LoadTestJob::class);
    Queue::assertPushed(LoadTestJob::class, fn (LoadTestJob $job): bool => $job->sleepSeconds === 1 && $job->messageGroup === null);
    Queue::assertPushed(LoadTestJob::class, 3);
});

it('sends the jobs in the given message group', function () {
    Queue::fake();

    artisan('queue:loadtest', ['queue' => 'default', 'count' => 2, '--group' => 'noisy-tenant'])->assertSuccessful();

    Queue::assertPushed(LoadTestJob::class, fn (LoadTestJob $job): bool => $job->messageGroup === 'noisy-tenant');
    Queue::assertPushed(LoadTestJob::class, 2);
});

it('refuses to run in production', function () {
    Queue::fake();

    app()->detectEnvironment(fn (): string => 'production');

    try {
        artisan('queue:loadtest', ['queue' => 'default', 'count' => 5])->assertFailed();
    } finally {
        // The test teardown migrates, which would otherwise ask to confirm running in production.
        app()->detectEnvironment(fn (): string => 'testing');
    }

    Queue::assertNothingPushed();
});
