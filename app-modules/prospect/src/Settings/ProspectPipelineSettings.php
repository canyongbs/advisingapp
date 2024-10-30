<?php

namespace AdvisingApp\Prospect\Settings;

use Spatie\LaravelSettings\Settings;

class ProspectPipelineSettings extends Settings
{
    public bool $is_enabled = false;

    public static function group(): string
    {
        return 'prospect_pipeline';
    }
}
