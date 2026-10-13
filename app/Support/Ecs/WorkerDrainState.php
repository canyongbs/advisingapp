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

namespace App\Support\Ecs;

use App\Enums\WorkerDrainReason;
use Carbon\CarbonImmutable;

/**
 * Task-local record that this worker task is draining: it takes no new jobs, finishes the ones it has, then releases
 * its ECS scale-in protection so ECS can stop it.
 */
class WorkerDrainState
{
    // The worker healthcheck in the ECS task definitions passes while this file exists, so keep the name in step.
    public const string FILENAME = 'advisingapp-worker-draining';

    // The healthcheck stops passing once the file is this old, so ECS replaces a task whose drain is stuck.
    public const int MAX_DRAIN_MINUTES = 30;

    public function __construct(
        private ?string $directory = null,
    ) {}

    public function isDraining(): bool
    {
        return $this->reason() !== null;
    }

    public function reason(): ?WorkerDrainReason
    {
        $reason = $this->read()['reason'] ?? null;

        return is_string($reason) ? WorkerDrainReason::tryFrom($reason) : null;
    }

    public function isProtectionReleased(): bool
    {
        return ($this->read()['protection_released'] ?? false) === true;
    }

    public function startedAt(): ?CarbonImmutable
    {
        $path = $this->path();

        clearstatcache(true, $path);

        $modifiedAt = @filemtime($path);

        return $modifiedAt === false ? null : CarbonImmutable::createFromTimestamp($modifiedAt);
    }

    public function isOverdue(): bool
    {
        return $this->startedAt()?->addMinutes(self::MAX_DRAIN_MINUTES)->isPast() ?? false;
    }

    public function start(WorkerDrainReason $reason): void
    {
        $this->write(['reason' => $reason->value, 'protection_released' => false]);
    }

    public function markProtectionReleased(): void
    {
        $reason = $this->reason();

        if ($reason === null) {
            return;
        }

        $startedAt = $this->startedAt();

        $this->write(['reason' => $reason->value, 'protection_released' => true]);

        // The healthcheck times the drain from the file's modification time.
        if ($startedAt !== null) {
            @touch($this->path(), $startedAt->getTimestamp());
        }
    }

    public function clear(): void
    {
        @unlink($this->path());
    }

    /**
     * @return array<string, mixed>
     */
    private function read(): array
    {
        $path = $this->path();

        if (! is_file($path)) {
            return [];
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $state = json_decode($contents, associative: true);

        return is_array($state) ? $state : [];
    }

    /**
     * @param array{reason: string, protection_released: bool} $state
     */
    private function write(array $state): void
    {
        file_put_contents($this->path(), json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    private function path(): string
    {
        return ($this->directory ?? sys_get_temp_dir()) . '/' . self::FILENAME;
    }
}
