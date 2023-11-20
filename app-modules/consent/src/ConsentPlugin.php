<?php

namespace Assist\Consent;

use Filament\Panel;
use Filament\Contracts\Plugin;

class ConsentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'consent';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Consent\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
