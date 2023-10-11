<?php

namespace Assist\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\Event;

class CreateEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting-center:create-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a calendar event for testing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Event::factory()->create();

        return self::SUCCESS;
    }
}
