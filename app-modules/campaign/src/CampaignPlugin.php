<?php

namespace Assist\Campaign;

use Filament\Panel;
use Filament\Contracts\Plugin;

class CampaignPlugin implements Plugin
{
    public function getId(): string
    {
        return 'campaign';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Campaign\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
