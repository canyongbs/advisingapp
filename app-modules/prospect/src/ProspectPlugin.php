<?php

namespace Assist\Prospect;

use Filament\Panel;
use Filament\Contracts\Plugin;

class ProspectPlugin implements Plugin
{
    public function getId(): string
    {
        return 'prospect';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Prospect\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
