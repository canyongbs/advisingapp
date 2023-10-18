<?php

namespace Assist\Campaign\DataTransferObjects;

use Spatie\LaravelData\Data;

class CampaignActionCreationData extends Data
{
    public function __construct(
        public string $type,
        public array $data,
    ) {}
}
