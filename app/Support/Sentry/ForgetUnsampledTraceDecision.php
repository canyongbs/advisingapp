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

use Illuminate\Support\Str;

/**
 * Queue payload hook that lets each job get its own Sentry sampling decision.
 *
 * Sentry skips a job outright when the request that queued it was not sampled, so the low request rate would also
 * hide most jobs queued from the UI, such as imports and AI responses. Dropping the parent's "not sampled" flag keeps
 * the trace ID but leaves the decision to TracesSampler. It must run after Sentry's own payload hook, which it does
 * because application service providers boot after package ones.
 */
class ForgetUnsampledTraceDecision
{
    private const string TRACE_PARENT_KEY = 'sentry_trace_parent_data';

    private const string BAGGAGE_KEY = 'sentry_baggage_data';

    private const string NOT_SAMPLED_SUFFIX = '-0';

    /**
     * @param ?array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    public function __invoke(?string $connection, ?string $queue, ?array $payload): array
    {
        $traceParent = $payload[self::TRACE_PARENT_KEY] ?? null;

        if ((! is_string($traceParent)) || (! str_ends_with($traceParent, self::NOT_SAMPLED_SUFFIX))) {
            return [];
        }

        $changes = [self::TRACE_PARENT_KEY => Str::beforeLast($traceParent, self::NOT_SAMPLED_SUFFIX)];

        $baggage = $payload[self::BAGGAGE_KEY] ?? null;

        if (is_string($baggage)) {
            $changes[self::BAGGAGE_KEY] = Str::of($baggage)
                ->explode(',')
                ->reject(fn (string $entry): bool => Str::startsWith(Str::trim($entry), 'sentry-sampled='))
                ->implode(',');
        }

        return $changes;
    }
}
