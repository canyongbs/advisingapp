<?php

namespace Assist\MeetingCenter\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Assist\MeetingCenter\CalendarManager;
use Assist\MeetingCenter\GoogleCalendarManager;

class SyncEvents extends Command
{
    /**
     * @var string
     */
    protected $signature = 'meeting-center:sync-events';

    /**
     * @var string
     */
    protected $description = 'Sync calendar events for testing.';

    public function handle(): int
    {
        $user = User::where('email', 'superadmin@assist.com')->first();

        $user->update([
            'calendar_type' => GoogleCalendarManager::type(),
            'calendar_id' => env('GOOGLE_CALENDAR_ID'),
        ]);

        resolve(CalendarManager::class)
            ->driver($user->calendar_type)
            ->syncEvents($user->calendar_id, $user);

        return self::SUCCESS;
    }
}
