<?php

namespace App\DataTransferObjects\LicenseManagement;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class LicenseData extends Data
{
    public function __construct(
        public Carbon $updatedAt,
        public LicenseSubscriptionData $subscription,
        public LicenseLimitsData $limits,
        public LicenseAddonsData $addons,
    ) {}
}
