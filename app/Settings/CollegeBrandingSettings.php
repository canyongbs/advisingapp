<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CollegeBrandingSettings extends Settings
{
    public bool $is_enabled = false;

    public ?string $college_text;

    public ?string $color = null;

    public static function group(): string
    {
        return 'theme';
    }
}
