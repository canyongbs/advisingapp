<?php

namespace Tests\Exceptions;

use Exception;

class StopMigration extends Exception
{
    public function __construct(string $message = 'Stop migrations')
    {
        parent::__construct($message);
    }
}
