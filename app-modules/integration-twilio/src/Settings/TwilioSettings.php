<?php

namespace Assist\IntegrationTwilio\Settings;

use Spatie\LaravelSettings\Settings;
use Assist\IntegrationTwilio\DataTransferObjects\TwilioApiKey;

class TwilioSettings extends Settings
{
    public ?TwilioApiKey $api_key;

    public static function group(): string
    {
        return 'twilio';
    }

    public static function encrypted(): array
    {
        return [
            'api_key',
        ];
    }
}
