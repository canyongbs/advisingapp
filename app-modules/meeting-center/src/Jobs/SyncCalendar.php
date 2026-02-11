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

namespace AdvisingApp\MeetingCenter\Jobs;

use AdvisingApp\MeetingCenter\Models\Calendar;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class SyncCalendar implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(protected Calendar $calendar)
    {
        $this->onQueue(config('meeting-center.queue'));
    }

    public function uniqueId(): string
    {
        return Tenant::current()->getKey() . ':' . $this->calendar->getKey();
    }

    public function handle(): void
    {
        if (blank($this->calendar->oauth_token)) {
            return;
        }

        $now = Carbon::now();
        $jobs = [];

        // Chunk 1: Current month + next month
        $jobs[] = new SyncCalendarPeriod(
            $this->calendar,
            $now->copy()->startOfMonth(),
            $now->copy()->addMonth()->endOfMonth()
        );

        // Chunk 2: Past 2 months
        $jobs[] = new SyncCalendarPeriod(
            $this->calendar,
            $now->copy()->subMonths(2)->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth()
        );

        // Chunks 3-7: Remaining future months for the next year
        for ($month = 2; $month <= 10; $month += 2) {
            $jobs[] = new SyncCalendarPeriod(
                $this->calendar,
                $now->copy()->addMonths($month)->startOfMonth(),
                $now->copy()->addMonths($month + 1)->endOfMonth()
            );
        }

        // Chunks 8-12: Remaining past months for the last year
        for ($month = 3; $month <= 11; $month += 2) {
            $jobs[] = new SyncCalendarPeriod(
                $this->calendar,
                $now->copy()->subMonths($month + 1)->startOfMonth(),
                $now->copy()->subMonths($month)->endOfMonth()
            );
        }

        Bus::chain($jobs)->dispatch();
    }
}
