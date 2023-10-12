<?php

namespace Assist\MeetingCenter;

use Exception;
use App\Models\User;
use Illuminate\Support\Manager;
use Assist\MeetingCenter\Contracts\Calendar;
use Illuminate\Contracts\Container\Container;

class CalendarManager extends Manager
{
    // TODO: maybe pass in user?
    // public function __construct(Container $container, protected User $user) {
    //     parent::__construct($container);
    // }

    public function createGoogleDriver(): Calendar
    {
        return new GoogleCalendarManager();
    }

    public function createOutlookDriver(): Calendar
    {
        return new OutlookCalendarManager();
    }

    /**
     *
     * @throws Exception
     */
    public function getDefaultDriver(): never
    {
        throw new Exception('A calendar drive must be explicitly declared.');
    }
}
