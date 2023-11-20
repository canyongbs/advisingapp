<?php

namespace Assist\MeetingCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Assist\MeetingCenter\Models\Calendar;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncCalendars implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        Calendar::cursor()
            ->each(
                fn (Calendar $calendar) => dispatch(new SyncCalendar($calendar))
            );
    }
}
