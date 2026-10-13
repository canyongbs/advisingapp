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

use App\Enums\WorkerDrainReason;
use App\Support\Ecs\WorkerDrainState;
use Illuminate\Support\Facades\File;

// Per process, so parallel test workers do not share drain state.
$directory = sys_get_temp_dir() . '/worker-drain-state-' . getmypid();

beforeEach(function () use ($directory) {
    File::deleteDirectory($directory);
    File::ensureDirectoryExists($directory);
});

afterEach(function () use ($directory) {
    File::deleteDirectory($directory);
});

it('records a drain in the file the worker healthcheck looks for', function () use ($directory) {
    $state = new WorkerDrainState($directory);

    expect($state->isDraining())->toBeFalse()
        ->and(is_file($directory . '/' . WorkerDrainState::FILENAME))->toBeFalse();

    $state->start(WorkerDrainReason::Deployment);

    expect($state->isDraining())->toBeTrue()
        ->and($state->reason())->toBe(WorkerDrainReason::Deployment)
        ->and($state->isProtectionReleased())->toBeFalse()
        ->and(is_file($directory . '/' . WorkerDrainState::FILENAME))->toBeTrue();
});

it('remembers that protection was released while keeping the reason', function () use ($directory) {
    $state = new WorkerDrainState($directory);
    $state->start(WorkerDrainReason::ScaleIn);

    $state->markProtectionReleased();

    expect($state->isProtectionReleased())->toBeTrue()
        ->and($state->reason())->toBe(WorkerDrainReason::ScaleIn);
});

it('does not record a release when not draining', function () use ($directory) {
    $state = new WorkerDrainState($directory);

    $state->markProtectionReleased();

    expect($state->isDraining())->toBeFalse()
        ->and($state->isProtectionReleased())->toBeFalse();
});

it('stops draining when cleared', function () use ($directory) {
    $state = new WorkerDrainState($directory);
    $state->start(WorkerDrainReason::Manual);

    $state->clear();

    expect($state->isDraining())->toBeFalse()
        ->and(is_file($directory . '/' . WorkerDrainState::FILENAME))->toBeFalse();
});

describe('drain age', function () use ($directory) {
    it('keeps the drain start time when protection is released', function () use ($directory) {
        $state = new WorkerDrainState($directory);
        $state->start(WorkerDrainReason::Deployment);

        $startedAt = now()->subMinutes(10)->getTimestamp();
        touch($directory . '/' . WorkerDrainState::FILENAME, $startedAt);

        $state->markProtectionReleased();

        expect($state->isProtectionReleased())->toBeTrue()
            ->and($state->startedAt()?->getTimestamp())->toBe($startedAt);
    });

    it('is overdue once it has drained longer than the healthcheck allows', function (int $minutesAgo, bool $isOverdue) use ($directory) {
        $state = new WorkerDrainState($directory);
        $state->start(WorkerDrainReason::ScaleIn);

        touch($directory . '/' . WorkerDrainState::FILENAME, now()->subMinutes($minutesAgo)->getTimestamp());

        expect($state->isOverdue())->toBe($isOverdue);
    })->with([
        'within the limit' => [29, false],
        'past the limit' => [31, true],
    ]);

    it('is not overdue when not draining', function () use ($directory) {
        expect((new WorkerDrainState($directory))->isOverdue())->toBeFalse();
    });
});
