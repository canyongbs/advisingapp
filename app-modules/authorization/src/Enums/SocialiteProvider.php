<?php

namespace Assist\Authorization\Enums;

use Exception;
use Mockery\MockInterface;
use SocialiteProviders\Manager\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;

enum SocialiteProvider: string
{
    case Azure = 'azure';

    public function driver(): Provider|MockInterface
    {
        return Socialite::driver($this->value);
    }

    public function config(): Config
    {
        return match ($this->value) {
            'azure' => new Config(
                config('services.azure.client_id'),
                config('services.azure.client_secret'),
                config('services.azure.redirect'),
                ['tenant' => config('services.azure.tenant_id', 'common')]
            ),
            default => throw new Exception('Invalid socialite provider'),
        };
    }
}
