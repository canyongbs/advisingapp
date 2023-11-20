<?php

namespace Assist\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\CalendarEvent;

class UpdateEvent extends Command
{
    /**
     * @var string
     */
    protected $signature = 'meeting-center:update-event';

    /**
     * @var string
     */
    protected $description = 'Update a calendar event for testing.';

    public function handle(): int
    {
        CalendarEvent::first()->update(['title' => fake()->words(asText: true)]);

        return self::SUCCESS;
    }
}
