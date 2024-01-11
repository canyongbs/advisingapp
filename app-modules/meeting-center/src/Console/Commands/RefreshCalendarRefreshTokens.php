<?php

namespace AdvisingApp\MeetingCenter\Console\Commands;

use Illuminate\Console\Command;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Jobs\RefreshCalendarRefreshToken;

class RefreshCalendarRefreshTokens extends Command
{
    protected $signature =
        'meeting-center:refresh-calendar-refresh-tokens';

    protected $description = 'Triggers a refresh of all calendar refresh tokens that are needed.';

    public function handle(): void
    {
        Calendar::query()
            ->whereNotNull('oauth_refresh_token')
            ->where('updated_at', '<=', now()->subDays(14))
            ->each(fn (Calendar $calendar) => RefreshCalendarRefreshToken::dispatch($calendar));
    }
}
