<?php

namespace Assist\IntegrationMicrosoftClarity\Settings;

use Spatie\LaravelSettings\Settings;

class MicrosoftClaritySettings extends Settings
{
    public bool $is_enabled;

    public ?string $id;

    public static function group(): string
    {
        return 'microsoft-clarity';
    }
}
