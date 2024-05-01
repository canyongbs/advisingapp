<?php

namespace App\Exceptions;

use Exception;
use App\Enums\Integration;

class IntegrationException extends Exception
{
    /**
     * @throws IntegrationNotConfigured
     * @throws IntegrationNotEnabled
     */
    public function __construct(Integration $integration)
    {
        if ($integration->isNotConfigured()) {
            throw new IntegrationNotConfigured($integration);
        }

        if ($integration->isOff()) {
            throw new IntegrationNotEnabled($integration);
        }

        parent::__construct("Something went wrong with the {$integration->getLabel()} integration.");
    }
}
