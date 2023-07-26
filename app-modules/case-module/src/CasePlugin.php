<?php

namespace Assist\CaseModule;

use Filament\Panel;
use Filament\Contracts\Plugin;

class CasePlugin implements Plugin
{
    public function getId(): string
    {
        return 'case-module';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\CaseModule\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
