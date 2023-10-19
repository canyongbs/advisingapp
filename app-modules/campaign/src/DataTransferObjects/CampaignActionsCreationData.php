<?php

namespace Assist\Campaign\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CampaignActionsCreationData extends Data
{
    public function __construct(
        #[DataCollectionOf(CampaignActionCreationData::class)]
        public DataCollection $actions,
    ) {}
}
