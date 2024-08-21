<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CollegeBrandingSettings extends Settings
{
    public bool $is_enabled;

    public ?string $college_text;

    public ?string $color;

    public static function group(): string
    {
        return 'college_branding';
    }
}
