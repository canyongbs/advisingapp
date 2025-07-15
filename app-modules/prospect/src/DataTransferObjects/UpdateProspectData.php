<?php

namespace AdvisingApp\Prospect\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateProspectData extends Data
{
    public function __construct(
        public string | Optional | null $firstName,
        public string | Optional | null $lastName,
        public string | Optional | null $fullName,
        public string | Optional | null $preferred,
        public string | Optional | null $description,
        public bool | Optional | null $smsOptOut,
        public bool | Optional | null $emailBounce,
        public string | null $status,
        public string | null $source,
        public string | Optional | null $birthdate,
        public int | Optional | null $hsgrad,
        public string | Optional | null $primaryEmailId = null,
    ) {}
}
