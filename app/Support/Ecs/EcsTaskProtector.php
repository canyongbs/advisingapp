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

use App\Exceptions\EcsTaskProtectionException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Acquires ECS task scale-in protection for the duration of a long-running job, so scale-in and rolling deployments
 * do not stop the task mid-job.
 *
 * Protection is only ever enabled, never disabled, and left to expire after the last long job starts: several worker
 * processes share one task, so a per-job release would let one worker strip a sibling's protection. A task-local
 * file coalesces requests so the rate-limited UpdateTaskProtection API is not called on every job.
 */
class EcsTaskProtector
{
    public const string STATE_FILENAME = 'advisingapp-ecs-task-protection';

    private const int REFRESH_BUFFER_SECONDS = 60;

    private const int HTTP_TIMEOUT_SECONDS = 5;

    private const int EXPIRY_HEADROOM_MINUTES = 5;

    public function protectForJobTimeout(int $jobTimeoutSeconds): void
    {
        $agentUri = $this->agentUri();

        if ($agentUri === null) {
            return;
        }

        if (! $this->needsRefresh($jobTimeoutSeconds)) {
            return;
        }

        $expiresInMinutes = (int) ceil($jobTimeoutSeconds / 60) + self::EXPIRY_HEADROOM_MINUTES;

        $this->request($agentUri, $expiresInMinutes);
    }

    private function agentUri(): ?string
    {
        $uri = Str::rtrim(Config::string('app.ecs_agent_uri'), '/');

        return blank($uri) ? null : $uri;
    }

    private function needsRefresh(int $jobTimeoutSeconds): bool
    {
        $protectedUntil = $this->readProtectedUntil();

        return $protectedUntil === null
            || $protectedUntil->subSeconds(self::REFRESH_BUFFER_SECONDS)->isBefore(CarbonImmutable::now()->addSeconds($jobTimeoutSeconds));
    }

    private function request(string $agentUri, int $expiresInMinutes): void
    {
        try {
            $response = Http::timeout(self::HTTP_TIMEOUT_SECONDS)
                ->acceptJson()
                ->put("{$agentUri}/task-protection/v1/state", [
                    'ProtectionEnabled' => true,
                    'ExpiresInMinutes' => $expiresInMinutes,
                ]);

            if ($response->successful()) {
                $this->rememberProtectedUntil(CarbonImmutable::now()->addMinutes($expiresInMinutes));

                return;
            }

            report(EcsTaskProtectionException::requestRejected($response->status()));
        } catch (Throwable $exception) {
            report(EcsTaskProtectionException::requestFailed($exception));
        }
    }

    private function statePath(): string
    {
        return sys_get_temp_dir() . '/' . self::STATE_FILENAME;
    }

    private function readProtectedUntil(): ?CarbonImmutable
    {
        $path = $this->statePath();

        if (! is_file($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $timestamp = Str::trim($contents);

        if (! ctype_digit($timestamp)) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp((int) $timestamp);
    }

    private function rememberProtectedUntil(CarbonImmutable $until): void
    {
        @file_put_contents($this->statePath(), (string) $until->getTimestamp(), LOCK_EX);
    }
}
