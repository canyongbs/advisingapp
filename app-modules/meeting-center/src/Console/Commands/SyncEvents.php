<?php

namespace Assist\MeetingCenter\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Assist\MeetingCenter\Managers\CalendarManager;

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
        $user = User::where('email', 'sampleadmin@advising.app')->first();

        resolve(CalendarManager::class)
            ->driver($user->calendar->provider_type->value)
            ->syncEvents($user->calendar);

        return self::SUCCESS;
    }
}
