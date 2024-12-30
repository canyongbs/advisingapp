<?php

namespace AdvisingApp\Authorization\Exceptions;

use Exception;

class InvalidAzureMatchingProperty extends Exception
{
    public function __construct(mixed $valueUsed)
    {
        parent::__construct('Invalid Azure matching property. Value used: ' . strval($valueUsed));
    }
}
