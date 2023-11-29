<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use App\DataTransferObjects\LicenseManagement\LicenseData;

class LicenseSettings extends Settings
{
    public ?string $license_key;

    public ?LicenseData $data;

    public static function group(): string
    {
        return 'license';
    }

    public static function encrypted(): array
    {
        return [
            'license_key',
            'data',
        ];
    }
}
