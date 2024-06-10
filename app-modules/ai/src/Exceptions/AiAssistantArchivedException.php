<?php

namespace AdvisingApp\Ai\Exceptions;

use Exception;

class AiAssistantArchivedException extends Exception
{
    public function __construct()
    {
        parent::__construct('This assistant has been archived and is no longer available to use.');
    }
}
