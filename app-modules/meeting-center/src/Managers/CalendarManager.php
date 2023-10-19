<?php

namespace Assist\MeetingCenter\Managers;

use Exception;
use Illuminate\Support\Manager;
use Assist\MeetingCenter\Managers\Contracts\CalendarInterface;

class CalendarManager extends Manager
{
    public function createGoogleDriver(): CalendarInterface
    {
        return new GoogleCalendarManager();
    }

    public function createOutlookDriver(): CalendarInterface
    {
        return new OutlookCalendarManager();
    }

    /**
     *
     * @throws Exception
     */
    public function getDefaultDriver(): never
    {
        throw new Exception('A calendar driver must be explicitly declared.');
    }
}
