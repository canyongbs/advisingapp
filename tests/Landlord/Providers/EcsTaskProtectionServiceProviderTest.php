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

use App\Providers\EcsTaskProtectionServiceProvider;
use App\Support\Ecs\EcsTaskProtector;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // The other JobProcessing listeners need a full job payload, so only this provider's listener is kept.
    Event::forget(JobProcessing::class);

    $provider = app()->getProvider(EcsTaskProtectionServiceProvider::class);

    assert($provider instanceof EcsTaskProtectionServiceProvider);

    $provider->boot();
});

function jobProcessingWithTimeout(?int $timeout): JobProcessing
{
    return new JobProcessing('sync', new SyncJob(app(), json_encode(['timeout' => $timeout]), 'sync', 'default'));
}

it('protects the ECS task while a job with a long timeout runs', function (int $timeout) {
    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldReceive('protectForJobTimeout')->once()->with($timeout);
    app()->instance(EcsTaskProtector::class, $protector);

    event(jobProcessingWithTimeout($timeout));
})->with([
    'at the threshold' => 100,
    'above the threshold' => 1140,
]);

it('does not protect the ECS task for a short or unset job timeout', function (?int $timeout) {
    $protector = Mockery::mock(EcsTaskProtector::class);
    $protector->shouldNotReceive('protectForJobTimeout');
    app()->instance(EcsTaskProtector::class, $protector);

    event(jobProcessingWithTimeout($timeout));
})->with([
    'below the threshold' => 99,
    'unset' => null,
]);
