<?php

namespace App\Exceptions;

use Exception;
use App\Enums\Integration;

class IntegrationNotConfigured extends Exception
{
    public function __construct(Integration $integration)
    {
        parent::__construct("The {$integration->getLabel()} integration is not configured.");
    }
}
