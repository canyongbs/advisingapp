<?php

namespace AdvisingApp\StockMedia\Settings;

use Spatie\LaravelSettings\Settings;

class StockMediaSettings extends Settings
{
    public bool $active = false;

    public ?string $provider = null;

    public ?string $pexels_api_key = null;

    public static function group(): string
    {
        return 'stock-media';
    }
}
