<?php

namespace Assist\IntegrationAI\Client\Concerns;

trait InitializesClient
{
    abstract protected function initializeClient(): void;
}
