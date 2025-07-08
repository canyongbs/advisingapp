<?php

namespace AdvisingApp\Prospect\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateProspectData extends Data
{
    /**
     * @param array<CreateProspectEmailAddressData> | Optional | null $emailAddresses
     */
    public function __construct(
        public string $sisid,
        public string | Optional | null $otherid,
        public string $first,
        public string $last,
        public string $fullName,
        public string | Optional | null $preferred,
        public string | Optional | null $birthdate,
        public int | Optional | null $hsgrad,
        public string | Optional | null $gender,
        public bool | Optional | null $smsOptOut,
        public bool | Optional | null $emailBounce,
        public bool | Optional | null $dual,
        public bool | Optional | null $ferpa,
        public bool | Optional | null $firstgen,
        public bool | Optional | null $sap,
        public string | Optional | null $holds,
        public string | Optional | null $dfw,
        public string | Optional | null $ethnicity,
        public string | Optional | null $lastlmslogin,
        public string | Optional | null $fETerm,
        public string | Optional | null $mrETerm,
        #[DataCollectionOf(CreateProspectEmailAddressData::class)]
        public array | Optional | null $emailAddresses,
    ) {}
}
