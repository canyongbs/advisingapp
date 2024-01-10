<?php

namespace AdvisingApp\Notification\Exceptions;

use Exception;

class NotificationQuotaExceeded extends Exception
{
    protected $message = 'Notification quota exceeded';
}
