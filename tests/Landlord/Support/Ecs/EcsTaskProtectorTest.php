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

use App\Exceptions\EcsTaskProtectionException;
use App\Support\Ecs\EcsTaskProtector;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;

// Per process, so parallel test workers do not share protection state.
$directory = sys_get_temp_dir() . '/ecs-task-protector-' . getmypid();

beforeEach(function () use ($directory) {
    config(['app.ecs_agent_uri' => '']);

    File::deleteDirectory($directory);
    File::ensureDirectoryExists($directory);
});

afterEach(function () use ($directory) {
    File::deleteDirectory($directory);
});

it('does not call ECS when the agent URI is absent', function () use ($directory) {
    Http::fake();

    (new EcsTaskProtector($directory))->protectForJobTimeout(600);

    Http::assertNothingSent();
});

it('enables protection with an expiry beyond the job timeout when on ECS', function () use ($directory) {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    (new EcsTaskProtector($directory))->protectForJobTimeout(600);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'http://169.254.170.2/api/task/task-protection/v1/state'
        && $request['ProtectionEnabled'] === true
        && $request['ExpiresInMinutes'] === 15);
});

it('does not re-request protection while an existing window is still valid', function () use ($directory) {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    $protector = new EcsTaskProtector($directory);
    $protector->protectForJobTimeout(600);
    $protector->protectForJobTimeout(600);

    Http::assertSentCount(1);
});

it('extends protection when a job could outlast the existing window', function () use ($directory) {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    $protector = new EcsTaskProtector($directory);
    $protector->protectForJobTimeout(100);
    $protector->protectForJobTimeout(1140);

    Http::assertSentCount(2);
    Http::assertSent(fn (Request $request): bool => $request['ExpiresInMinutes'] === 24);
});

it('does not shorten a longer window that another worker is setting at the same time', function () use ($directory) {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    $lockedMarker = "{$directory}/locked";

    // Stands in for a worker part-way through requesting 24 minutes for a 1140s job: it holds the lock, then records it.
    $otherWorker = Process::start([PHP_BINARY, '-r', sprintf(
        '$lock = fopen(%s, "c"); flock($lock, LOCK_EX); touch(%s); usleep(500000); file_put_contents(%s, (string) (time() + 24 * 60)); flock($lock, LOCK_UN);',
        var_export($directory . '/' . EcsTaskProtector::LOCK_FILENAME, true),
        var_export($lockedMarker, true),
        var_export($directory . '/' . EcsTaskProtector::STATE_FILENAME, true),
    )]);

    $deadline = microtime(true) + 5;

    while ((! is_file($lockedMarker)) && (microtime(true) < $deadline)) {
        Sleep::usleep(10000);
    }

    expect(is_file($lockedMarker))->toBeTrue();

    $protector = new EcsTaskProtector($directory);
    $protector->protectForJobTimeout(600);

    expect($otherWorker->wait()->successful())->toBeTrue();

    Http::assertNothingSent();

    expect(CarbonImmutable::now()->diffInMinutes($protector->protectedUntil()))->toBeGreaterThan(23);
});

it('reports rejected protection requests with the reason, without throwing', function (int $status, array $body, string $message) use ($directory) {
    Exceptions::fake();
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response($body, $status)]);

    $protector = new EcsTaskProtector($directory);
    $protector->protectForJobTimeout(600);

    Exceptions::assertReported(fn (EcsTaskProtectionException $exception): bool => $exception->getMessage() === $message);

    expect($protector->protectedUntil())->toBeNull();
})->with([
    'an error status' => [500, [], 'ECS rejected the task scale-in protection update with status 500.'],
    'a failure in a successful response' => [200, ['failure' => ['Reason' => 'TASK_NOT_VALID', 'Detail' => null]], 'ECS rejected the task scale-in protection update with status 200: TASK_NOT_VALID.'],
    'an error calling ECS' => [200, ['error' => ['Code' => 'ThrottlingException', 'Message' => 'Rate exceeded']], 'ECS rejected the task scale-in protection update with status 200: ThrottlingException Rate exceeded.'],
]);

it('does not report protection ECS refuses while it waits to stop a protected task, and asks again on the next job', function () use ($directory) {
    Exceptions::fake();
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task', 'queue-autoscale.manager.log_channel' => 'null']);
    Http::fakeSequence()
        ->push(['failure' => ['Reason' => 'DEPLOYMENT_BLOCKED', 'Detail' => null]], 200)
        ->push([], 200);

    $protector = new EcsTaskProtector($directory);
    $protector->protectForJobTimeout(600);

    Exceptions::assertNothingReported();

    expect($protector->protectedUntil())->toBeNull();

    $protector->protectForJobTimeout(600);

    Http::assertSentCount(2);

    expect($protector->protectedUntil())->not->toBeNull();
});

it('reports failed protection requests without throwing', function () use ($directory) {
    Exceptions::fake();
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(fn () => throw new ConnectionException('connection refused'));

    (new EcsTaskProtector($directory))->protectForJobTimeout(600);

    Exceptions::assertReported(EcsTaskProtectionException::class);
});

describe('release', function () use ($directory) {
    it('disables protection and forgets the window, so the next long job protects the task again', function () use ($directory) {
        config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
        Http::fake(['*' => Http::response([], 200)]);

        $protector = new EcsTaskProtector($directory);
        $protector->protectForJobTimeout(600);

        expect($protector->protectedUntil())->not->toBeNull()
            ->and($protector->release())->toBeTrue()
            ->and($protector->protectedUntil())->toBeNull();

        Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
            && $request['ProtectionEnabled'] === false
            && ! isset($request['ExpiresInMinutes']));

        $protector->protectForJobTimeout(600);

        Http::assertSentCount(3);
    });

    it('keeps the window when ECS rejects the release', function () use ($directory) {
        Exceptions::fake();
        config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
        Http::fakeSequence()
            ->push([], 200)
            ->push([], 500);

        $protector = new EcsTaskProtector($directory);
        $protector->protectForJobTimeout(600);

        expect($protector->release())->toBeFalse()
            ->and($protector->protectedUntil())->not->toBeNull();

        Exceptions::assertReported(EcsTaskProtectionException::class);
    });

    it('has nothing to release when not on ECS', function () use ($directory) {
        Http::fake();

        expect((new EcsTaskProtector($directory))->release())->toBeTrue();

        Http::assertNothingSent();
    });
});
