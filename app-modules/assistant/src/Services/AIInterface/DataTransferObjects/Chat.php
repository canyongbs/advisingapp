<?php

namespace Assist\Assistant\Services\AIInterface\DataTransferObjects;

use Livewire\Wireable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class Chat extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        #[DataCollectionOf(ChatMessage::class)]
        public DataCollection $messages,
    ) {}
}
