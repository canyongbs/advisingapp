<?php

namespace Assist\Engagement;

use Filament\Panel;
use Filament\Contracts\Plugin;

class EngagementPlugin implements Plugin
{
    public function getId(): string
    {
        return 'engagement';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Engagement\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
