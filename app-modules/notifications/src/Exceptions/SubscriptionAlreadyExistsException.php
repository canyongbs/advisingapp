<?php

namespace Assist\Notifications\Exceptions;

use Exception;

class SubscriptionAlreadyExistsException extends Exception
{
    protected $message = 'Subscription already exists';
}
