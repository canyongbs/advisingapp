<?php

namespace Assist\ServiceManagement\Exceptions;

use Exception;

class ServiceRequestNumberUpdateAttemptException extends Exception
{
    protected $message = 'It is not allowed to change the service_request_number';
}
