<?php

namespace App\DataTransferObjects\LicenseManagement;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class LicenseSubscriptionData extends Data
{
    public function __construct(
        public string $clientName,
        public string $partnerName,
        public string $clientPo,
        public string $partnerPo,
        public Carbon $startDate,
        public Carbon $endDate,
    ) {}
}
