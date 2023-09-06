<?php

namespace Assist\ServiceManagement\Exceptions;

use Exception;

class ServiceRequestNumberExceededReRollsException extends Exception
{
    protected $message = 'The service_request_number has exceeded the maximum number of re-rolls';
}
