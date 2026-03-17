<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateStudentData extends Data
{
    /**
     * @param array<CreateStudentEmailAddressData> | Optional | null $emailAddresses
     * @param array<CreateStudentPhoneNumberData> | Optional | null $phoneNumbers
     */
    public function __construct(
        public string $sisid,
        public string | Optional | null $otherid,
        public string $first,
        public string $last,
        public string $fullName,
        public string | Optional | null $preferred,
        public string | Optional | null $birthdate,
        public string | Optional | null $hsgrad,
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
        public string | Optional | null $fETerm, // @phpstan-ignore-line
        public string | Optional | null $mrETerm, // @phpstan-ignore-line
        #[DataCollectionOf(CreateStudentEmailAddressData::class)]
        public array | Optional | null $emailAddresses,
        #[DataCollectionOf(CreateStudentPhoneNumberData::class)]
        public array | Optional | null $phoneNumbers,
    ) {}
}
