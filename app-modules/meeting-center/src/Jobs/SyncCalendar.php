<?php

namespace Assist\MeetingCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Assist\MeetingCenter\Models\Calendar;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\MeetingCenter\Managers\CalendarManager;

class SyncCalendar implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Calendar $calendar) {}

    public function handle(): void
    {
        resolve(CalendarManager::class)
            ->driver($this->calendar->type)
            ->syncEvents($this->calendar);
    }
}
