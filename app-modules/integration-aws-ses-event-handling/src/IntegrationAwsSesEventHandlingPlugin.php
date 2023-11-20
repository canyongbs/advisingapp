<?php

namespace Assist\IntegrationAwsSesEventHandling;

use Filament\Panel;
use Filament\Contracts\Plugin;

class IntegrationAwsSesEventHandlingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integration-aws-ses-event-handling';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\IntegrationAwsSesEventHandling\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
