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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['app.ecs_agent_uri' => '']);
    @unlink(sys_get_temp_dir() . '/' . EcsTaskProtector::STATE_FILENAME);
});

afterEach(function () {
    @unlink(sys_get_temp_dir() . '/' . EcsTaskProtector::STATE_FILENAME);
});

it('does not call ECS when the agent URI is absent', function () {
    Http::fake();

    app(EcsTaskProtector::class)->protectForJobTimeout(600);

    Http::assertNothingSent();
});

it('enables protection with an expiry beyond the job timeout when on ECS', function () {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    app(EcsTaskProtector::class)->protectForJobTimeout(600);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && $request->url() === 'http://169.254.170.2/api/task/task-protection/v1/state'
        && $request['ProtectionEnabled'] === true
        && $request['ExpiresInMinutes'] === 15);
});

it('does not re-request protection while an existing window is still valid', function () {
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 200)]);

    $protector = app(EcsTaskProtector::class);
    $protector->protectForJobTimeout(600);
    $protector->protectForJobTimeout(600);

    Http::assertSentCount(1);
});

it('reports rejected protection requests without throwing', function () {
    Exceptions::fake();
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(['*' => Http::response([], 500)]);

    app(EcsTaskProtector::class)->protectForJobTimeout(600);

    Exceptions::assertReported(EcsTaskProtectionException::class);
});

it('reports failed protection requests without throwing', function () {
    Exceptions::fake();
    config(['app.ecs_agent_uri' => 'http://169.254.170.2/api/task']);
    Http::fake(fn () => throw new ConnectionException('connection refused'));

    app(EcsTaskProtector::class)->protectForJobTimeout(600);

    Exceptions::assertReported(EcsTaskProtectionException::class);
});
