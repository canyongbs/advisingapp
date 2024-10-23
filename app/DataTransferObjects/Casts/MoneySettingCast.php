<?php

namespace App\DataTransferObjects\Casts;

use Cknow\Money\Money;
use Spatie\LaravelSettings\SettingsCasts\SettingsCast;

class MoneySettingCast implements SettingsCast
{
    public function get($payload): ?Money
    {
        if (blank($payload) || ! is_array($payload)) {
            return null;
        }

        $value = intval($payload['value']) ?? null;
        $currency = $payload['currency'] ?? null;

        if (blank($value) || blank($currency) || ! is_int($value) || ! is_string($currency)) {
            return null;
        }

        return Money::parse($value, $currency);
    }

    public function set($payload): ?array
    {
        if (blank($payload) || ! ($payload instanceof Money)) {
            return null;
        }

        return [
            'value' => $payload->getAmount(),
            'currency' => $payload->getCurrency()->getCode(),
        ];
    }
}
