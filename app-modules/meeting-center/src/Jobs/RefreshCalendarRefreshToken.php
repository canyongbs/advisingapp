<?php

namespace AdvisingApp\MeetingCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;

class RefreshCalendarRefreshToken implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Calendar $calendar) {}

    public function handle(): void
    {
        /** @var CalendarInterface $calendarManager */
        $calendarManager = resolve(CalendarManager::class)
            ->driver($this->calendar->provider_type->value);

        $calendarManager->refreshToken($this->calendar);
    }
}
