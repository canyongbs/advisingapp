<?php

namespace App\Settings;

use Cknow\Money\Money;
use Spatie\LaravelSettings\Settings;
use App\DataTransferObjects\Casts\MoneySettingCast;

class ProspectConversionSettings extends Settings
{
    public ?Money $estimated_average_revenue;

    public static function group(): string
    {
        return 'prospect-conversion';
    }

    public static function casts(): array
    {
        return [
            'estimated_average_revenue' => MoneySettingCast::class,
        ];
    }
}
