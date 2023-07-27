<?php

namespace Assist\Case;

use Filament\Panel;
use Filament\Contracts\Plugin;

class CasePlugin implements Plugin
{
    public function getId(): string
    {
        return 'case';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Case\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
