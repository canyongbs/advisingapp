<?php

namespace Assist\Assistant\Services\AIInterface\DataTransferObjects;

use Livewire\Wireable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Concerns\WireableData;

class ChatMessage extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string $message,
        public string $from,
    ) {}
}
