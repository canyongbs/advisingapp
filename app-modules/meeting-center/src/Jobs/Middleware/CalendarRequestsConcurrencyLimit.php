<?php

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

        Redis::funnel("calendar-concurrency-{$job->calendar->provider_id}")
            ->block(10)
            ->limit(4)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                $job->release(10);
            });
    }
}
