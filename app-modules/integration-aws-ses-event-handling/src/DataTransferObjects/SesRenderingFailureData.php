<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesRenderingFailureData extends Data
{
    public function __construct(
        public string $templateName,
        public string $errorMessage,
    ) {}
}
