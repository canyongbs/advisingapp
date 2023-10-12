<?php

namespace Assist\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\Event;

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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Event::first()->update(['title' => fake()->words(asText: true)]);

        return self::SUCCESS;
    }
}
