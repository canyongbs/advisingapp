<?php

namespace AdvisingApp\Prospect\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateProspectEmailAddressData extends Data
{
    public function __construct(
        public string | Optional | null $address,
        public string | Optional | null $type,
        public int | Optional | null $order,
    ) {}
}
