<?php

namespace Assist\MeetingCenter\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Assist\MeetingCenter\Models\Event;
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

        $user->update([
            'calendar_type' => GoogleCalendarManager::type(),
            'calendar_id' => env('GOOGLE_CALENDAR_ID'),
        ]);

        Event::factory()->for($user)->create();

        return self::SUCCESS;
    }
}
