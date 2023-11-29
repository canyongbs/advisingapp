<?php

namespace App\DataTransferObjects\LicenseManagement;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

#[MapInputName(SnakeCaseMapper::class)]
class LicenseLimitsData extends Data
{
    public function __construct(
        public int $crmSeats,
        public int $analyticsSeats,
        public int $emails,
        public int $sms,
        // TODO: Need to confirm if this is the best way to work with it being just a day and month, no year.
        #[WithCast(DateTimeInterfaceCast::class, format: 'd-m')]
        public Carbon $resetDate,
    ) {}
}
