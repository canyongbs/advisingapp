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

namespace App\Support\Sentry;

use App\Jobs\DispatchForEachTenant;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use Sentry\Tracing\SamplingContext;

/**
 * Decides what share of each kind of request and job Sentry traces, using the rates in `config/sentry-sampling.php`.
 *
 * Referenced as a static method callable in `config/sentry.php`, because a closure there cannot be config-cached.
 */
class TracesSampler
{
    /**
     * @var array<string, ?int>
     */
    private static array $declaredTimeouts = [];

    public static function sample(SamplingContext $context): float
    {
        // Keeps a distributed trace whole. Jobs only see a parent decision when it was to sample: see ForgetUnsampledTraceDecision.
        $parentSampled = $context->getParentSampled();

        if ($parentSampled !== null) {
            return $parentSampled ? 1.0 : 0.0;
        }

        $transaction = $context->getTransactionContext();

        if ($transaction === null) {
            return 0.0;
        }

        return match ($transaction->getOp()) {
            'queue.process' => self::jobRate($transaction->getName()),
            'http.server' => self::requestRate($transaction->getName()),
            default => 0.0,
        };
    }

    private static function jobRate(string $job): float
    {
        if (in_array($job, Config::array('sentry-sampling.untraced_jobs'), strict: true)) {
            return 0.0;
        }

        if (in_array($job, Config::array('sentry-sampling.high_volume_jobs'), strict: true) || is_subclass_of($job, DispatchForEachTenant::class)) {
            return Config::float('sentry-sampling.rates.high_volume_jobs');
        }

        if ((self::declaredTimeout($job) ?? 0) >= Config::integer('sentry-sampling.long_job_timeout_seconds')) {
            return Config::float('sentry-sampling.rates.long_jobs');
        }

        return Config::float('sentry-sampling.rates.jobs');
    }

    private static function requestRate(string $path): float
    {
        if (in_array($path, Config::array('sentry-sampling.untraced_paths'), strict: true)) {
            return 0.0;
        }

        if (preg_match('#^/livewire[^/]*/update$#', $path) === 1) {
            return Config::float('sentry-sampling.rates.livewire_updates');
        }

        return Config::float('sentry-sampling.rates.requests');
    }

    /**
     * The `$timeout` the job class declares, not the worker's default for jobs without one.
     */
    private static function declaredTimeout(string $job): ?int
    {
        if (array_key_exists($job, self::$declaredTimeouts)) {
            return self::$declaredTimeouts[$job];
        }

        $timeout = class_exists($job) ? ((new ReflectionClass($job))->getDefaultProperties()['timeout'] ?? null) : null;

        return self::$declaredTimeouts[$job] = is_int($timeout) ? $timeout : null;
    }
}
