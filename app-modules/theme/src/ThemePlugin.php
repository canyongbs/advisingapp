<?php

namespace Assist\Theme;

use Filament\Panel;
use Filament\Contracts\Plugin;

class ThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'theme';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'Assist\\Theme\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void {}
}
