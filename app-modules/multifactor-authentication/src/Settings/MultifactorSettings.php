<?php

namespace AdvisingApp\MultifactorAuthentication\Settings;

use Spatie\LaravelSettings\Settings;

class MultifactorSettings extends Settings
{
    public bool $required = false;

    public static function group(): string
    {
        return 'multifactor';
    }
}
