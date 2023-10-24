<?php

namespace Assist\Theme\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public bool $is_logo_active;

    public bool $is_favicon_active;

    public static function group(): string
    {
        return 'theme';
    }
}
