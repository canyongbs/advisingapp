<?php

namespace AdvisingApp\Authorization\Exceptions;

use Exception;

class NoMatchingCredentialsException extends Exception
{
    public function __construct()
    {
        parent::__construct('No credentials matching the given client secret were found.');
    }
}
