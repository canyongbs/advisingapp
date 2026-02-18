<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Jobs\Middleware;

use AdvisingApp\MeetingCenter\Enums\CalendarProvider;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendarPeriod;
use Closure;
use Illuminate\Support\Facades\Redis;

class CalendarRequestsConcurrencyLimit
{
    /**
     * @param Closure(object): void $next
     */
    public function handle(SyncCalendarPeriod $job, Closure $next): void
    {
        $provider = $job->calendar->provider_type;

        if ($provider !== CalendarProvider::Outlook) {
            // Only apply concurrency limit to Outlook calendars
            $next($job);

            return;
        }

        Redis::funnel("{calendar-concurrency-{$job->calendar->provider_id}}")
            ->block(10)
            ->limit(4)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                $job->release(10);
            });
    }
}
