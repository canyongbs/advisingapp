<?php

namespace Assist\IntegrationGoogleAnalytics\Settings;

use Spatie\LaravelSettings\Settings;

class GoogleAnalyticsSettings extends Settings
{
    public bool $is_enabled;

    public ?string $id;

    public static function group(): string
    {
        return 'google-analytics';
    }
}
