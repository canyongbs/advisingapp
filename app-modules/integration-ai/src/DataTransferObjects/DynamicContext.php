<?php

namespace Assist\IntegrationAI\DataTransferObjects;

use Spatie\LaravelData\Data;
use Illuminate\Database\Eloquent\Model;

class DynamicContext extends Data
{
    public Model $record;

    public ?string $context;

    public function __construct()
    {
        $this->context = $this->record->getDynamicContext();
    }
}
