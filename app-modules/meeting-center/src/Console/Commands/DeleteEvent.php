<?php

namespace Assist\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\Event;

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

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Event::first()->delete();

        return self::SUCCESS;
    }
}
