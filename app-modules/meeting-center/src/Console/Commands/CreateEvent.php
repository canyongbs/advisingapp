<?php

namespace Assist\MeetingCenter\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\GoogleCalendarManager;

class CreateEvent extends Command
{
    /**
     * @var string
     */
    protected $signature = 'meeting-center:create-event';

    /**
     * @var string
     */
    protected $description = 'Create a calendar event for testing.';

    public function handle(): int
    {
        $user = User::where('email', 'superadmin@assist.com')->first();

        // dd((new GoogleCalendarManager())->getEvents($user->calendar));

        $user->calendar
            ->events()
            ->create(CalendarEvent::factory()->for($user->calendar)->make()->toArray());

        return self::SUCCESS;
    }
}
