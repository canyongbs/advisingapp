<?php

namespace AdvisingApp\IntegrationTwilio;

use Filament\Panel;
use Filament\Contracts\Plugin;

class IntegrationTwilioPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integration-twilio';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'AdvisingApp\\IntegrationTwilio\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void {}
}
