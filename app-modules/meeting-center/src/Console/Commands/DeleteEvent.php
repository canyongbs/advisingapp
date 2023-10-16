<?php

namespace Assist\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\CalendarEvent;

class DeleteEvent extends Command
{
    /**
     * @var string
     */
    protected $signature = 'meeting-center:delete-event';

    /**
     * @var string
     */
    protected $description = 'Delete a calendar event for testing.';

    public function handle(): int
    {
        CalendarEvent::first()->delete();

        return self::SUCCESS;
    }
}
