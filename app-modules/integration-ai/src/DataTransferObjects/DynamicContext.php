<?php

namespace Assist\IntegrationAI\DataTransferObjects;

use Spatie\LaravelData\Data;
use Assist\IntegrationAi\Models\Concerns\ProvidesDynamicContext;

class DynamicContext extends Data
{
    public ProvidesDynamicContext $record;

    public ?string $context;

    public function __construct()
    {
        $this->context = $this->record->getDynamicContext();
    }
}
