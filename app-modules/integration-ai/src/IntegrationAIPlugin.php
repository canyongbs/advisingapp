<?php

namespace Assist\IntegrationAI;

use Filament\Panel;
use Filament\Contracts\Plugin;

class IntegrationAIPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integration-ai';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\IntegrationAI\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
