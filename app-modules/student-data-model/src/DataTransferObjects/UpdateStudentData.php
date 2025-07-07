<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateStudentData extends Data
{
    public function __construct(
        public string | Optional | null $otherid,
        public string | Optional | null $first,
        public string | Optional | null $last,
        public string | Optional | null $fullName,
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
        public string | Optional | null $primaryEmailId = null,
    ) {}
}
