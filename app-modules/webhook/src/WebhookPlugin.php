<?php

namespace Assist\Webhook;

use Filament\Panel;
use Filament\Contracts\Plugin;

class WebhookPlugin implements Plugin
{
    public function getId(): string
    {
        return 'webhook';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Webhook\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
